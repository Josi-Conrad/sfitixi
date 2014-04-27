<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 21:31
 */

namespace Tixi\App\AppBundle\Address;


use Tixi\ApiBundle\Helper\ClientIdService;
use Tixi\ApiBundle\Helper\StringService;
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\CoreDomain\Address;

class AddressLookupServiceGoogle extends AddressLookupService{

    const APIBASEURL = 'https://maps.googleapis.com/maps/api/geocode/json';

   /* Maps google service status codes. For more detail see: https://developers.google.com/maps/documentation/geocoding/#StatusCodes
    *
    *"OK" indicates that no errors occurred; the address was successfully parsed and at least one geocode was returned.
    *"ZERO_RESULTS" indicates that the geocode was successful but returned no results. This may occur if the geocoder was passed a non-existent address.
    *"OVER_QUERY_LIMIT" indicates that you are over your quota.
    *"REQUEST_DENIED" indicates that your request was denied, generally because of lack of a sensor parameter.
    *"INVALID_REQUEST" generally indicates that the query (address, components or latlng) is missing.
    *"UNKNOWN_ERROR" indicates that the request could not be processed due to a server error. The request may succeed if you try again.
    */
    protected $googleStatusCodeMapper = array(
        "OK" => 1,
        "ZERO_RESULTS" => 2,
        "OVER_QUERY_LIMIT" => 3,
        "REQUEST_DENIED" => 4,
        "INVALID_REQUEST" => 5,
        "UNKNOWN_ERROR" => 6
    );


    public function hasLookupQuota()
    {
        return true;
    }

    protected function getAddressHandlingDTOs($lookupStr)
    {

        $url = $this->constructApiURL($lookupStr);
        $jsonResponseString = file_get_contents($url);
        $responseObject = json_decode($jsonResponseString);
        $statusCode = $this->googleStatusCodeMapper[$responseObject->status];
        $dtos = array();
        if($statusCode===1 || $statusCode ===2) {
            $results = $responseObject->results;
            foreach($results as $key=>$result) {
                if($key>=$this->lookupLimit) {
                    break;
                }
                $dtos[] = $this->parseRepsonseToDTO($result);
            }
        }elseif($statusCode===3) {
            throw new AddressLookupQuotaExceededException();
        }else {
            throw new AddressLookupBadResponseException();
        }
        return $dtos;

    }

    protected function getMaxDailyQuota() {
        return 2500;
    }

    protected function parseRepsonseToDTO($responseObject) {
        $name= null;
        $streetNr=null;
        $streetName=null;
        $postalCode=null;
        $city=null;
        $country=null;
        $lat=null;
        $long=null;

        $name = $responseObject->formatted_address;
        $lat = GeometryService::serialize($responseObject->geometry->location->lat);
        $lng = GeometryService::serialize($responseObject->geometry->location->lng);

        $addressComponents = $responseObject->address_components;
        foreach($addressComponents as $addressComponent) {
            $types = $addressComponent->types;
            if(in_array('street_number',$types)) {
                $streetNr = $this->getLongName($addressComponent);
            }elseif(in_array('route',$types)) {
                $streetName = $this->getLongName($addressComponent);
            }elseif(in_array('administrative_area_level_2',$types)) {
                $city = $this->getLongName($addressComponent);
            }elseif(in_array('administrative_area_level_1',$types)) {
                if(null === $city) {
                    $city = $this->getLongName($addressComponent);
                }
            }elseif(in_array('country',$types)) {
                $country = $this->getLongName($addressComponent);
            }elseif(in_array('postal_code',$types)) {
                $postalCode = $this->getLongName($addressComponent);
            }
        }

        $street = $streetName;
        if(null !== $streetNr) {
            $street.=' '.$streetNr;
        }

        $dto = new AddressHandleDTO();
        $dto->name = $name;
        $dto->street = $street;
        $dto->postalCode = $postalCode;
        $dto->city = $city;
        $dto->country = $country;
        $dto->lat = $lat;
        $dto->lng = $lng;
        $dto->source = Address::SOURCE_GOOGLE;
        return $dto;
    }

    protected function getLongName($addressComponent) {
        return $addressComponent->long_name;
    }

    protected function constructApiURL($lookupStr) {
        /** @var ClientIdService $clientIdService */
        $clientIdService = $this->container->get('tixi_api.clientidservice');
        $uncheckedClientId = $this->container->getParameter('tixi_parameter_client');
        $googleApiKey = $this->container->getParameter('tixi_parameter_google_apikey');

        $clientId = $clientIdService->getClientId($uncheckedClientId);
        $keywords = $this->constructKeywordArray($lookupStr);
        $region = $this->constructRegion($clientId);
        $components = $this->constructComponents($clientId, $region, $keywords);

        $url = self::APIBASEURL.'?';
        $urlParameters = array();
        $keywordSection = $this->constructKeywordSection($keywords);
        if('' !== $keywordSection) {
            $urlParameters[] = $keywordSection;
        }
        $componentSection = $this->constructComponentsSection($components);
        if('' !== $componentSection) {
            $urlParameters[] = $componentSection;
        }
        $regionSection = $this->constructRegionSection($region);
        if('' !== $regionSection) {
            $urlParameters[] = $regionSection;
        }
        $languageSection = $this->constructLanguageSection($region);
        if('' !== $languageSection) {
            $urlParameters[] = $languageSection;
        }
        $apiKeySection = $this->constructApiKeySection($googleApiKey);
        if('' !== $apiKeySection) {
            $urlParameters[] = $apiKeySection;
        }
        $urlParameters[] = $this->constructAdditionalSection();
        $urlParameterString = implode('&',$urlParameters);
        $urlParameterString = StringService::convertStringToASCII($urlParameterString);
        $url .= $urlParameterString;

//        $url = StringService::convertStringToASCII($url);
        return $url;
    }

    protected function constructKeywordArray($lookupStr) {
        return explode(' ',strtolower($lookupStr));
    }

    protected function constructKeywordSection(array $keywordsArray=array()) {
        $keywordsString ='';
        $keywords = implode('+',$keywordsArray);
        if('' !== $keywords) {
            $keywordsString.='address='.$keywords;
        }
        return $keywordsString;
    }

    protected function constructRegion($clientId) {
        $regionStr = '';
        if($clientId===ClientIdService::ZUGID) {
            $regionStr = 'CH';
        }
        return $regionStr;
    }

    protected function constructRegionSection($region='') {
        $regionString = '';
        if('' !== $region) {
            $regionString.='region='.$region;
        }
        return $regionString;
    }

    protected function constructComponents($clientId, $region, $keywords) {
        $componentArray = array();
        if($clientId===ClientIdService::ZUGID) {
            if(!$this->containsRegionInformation($region, $keywords)) {
                $componentArray[] = 'administrative_area:ZG';
            }
        }
        return $componentArray;
    }

    protected function constructComponentsSection(array $componentsArray=array()) {
        $componentsString = '';
        $components = implode('|',$componentsArray);
        if($components) {
            $componentsString.='components='.$components;
        }
        return $componentsString;
    }

    protected function constructAdditionalSection() {
        return 'sensor=false';
    }

    protected function constructApiKeySection($apiKey='') {
        $apiKeyString = '';
        if('' !== $apiKey) {
            $apiKeyString.='key='.$apiKey;
        }
        return $apiKeyString;
    }

    protected function constructLanguageSection($region='') {
        $languageString = '';
        if($region==='CH') {
            $languageString.='language=de';
        }
        return $languageString;
    }

    protected function containsRegionInformation($region, $keywords) {
        $compareArray = array();
        if($region==='CH') {
            $compareArray[] = 'zürich';
            $compareArray[] = 'bern';
            $compareArray[] = 'luzern';
            $compareArray[] = 'uri';
            $compareArray[] = 'schwyz';
            $compareArray[] = 'obwalden';
            $compareArray[] = 'nidwalden';
            $compareArray[] = 'glarus';
            $compareArray[] = 'zug';
            $compareArray[] = 'freiburg';
            $compareArray[] = 'solothurn';
            $compareArray[] = 'basel';
            $compareArray[] = 'schaffhausen';
            $compareArray[] = 'appenzell';
            $compareArray[] = 'stgallen';
            $compareArray[] = 'graubünden';
            $compareArray[] = 'aargau';
            $compareArray[] = 'thurgau';
            $compareArray[] = 'tessin';
            $compareArray[] = 'waadt';
            $compareArray[] = 'wallis';
            $compareArray[] = 'neuenburg';
            $compareArray[] = 'genf';
            $compareArray[] = 'jura';
        }
        return (count(array_intersect($keywords, $compareArray))>0);
    }

}