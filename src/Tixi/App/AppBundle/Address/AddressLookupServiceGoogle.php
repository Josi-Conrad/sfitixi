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

/**
 * Class AddressLookupServiceGoogle
 * @package Tixi\App\AppBundle\Address
 */
class AddressLookupServiceGoogle extends AddressLookupService {

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

    /**
     * @return bool
     */
    public function hasLookupQuota() {
        return true;
    }

    /**
     * @param $lookupStr
     * @return array
     * @throws AddressLookupBadResponseException
     * @throws AddressLookupQuotaExceededException
     */
    protected function getAddressHandlingDTOs($lookupStr) {
        $url = $this->constructApiURL($lookupStr);
//        $jsonResponseString = file_get_contents($url);
        $jsonResponseString = $this->getJSONResponse($url);
        $responseObject = json_decode($jsonResponseString);
        $statusCode = $this->googleStatusCodeMapper[$responseObject->status];
        $dtos = array();
        if ($statusCode === 1 || $statusCode === 2) {
            $results = $responseObject->results;
            foreach ($results as $key => $result) {
                if ($key >= $this->lookupLimit) {
                    break;
                }
                $dtos[] = $this->parseResponseToDTO($result);
            }
        } elseif ($statusCode === 3) {
            throw new AddressLookupQuotaExceededException();
        } else {
            throw new AddressLookupBadResponseException();
        }
        return $dtos;

    }

    /**
     * @param $lookupStr
     * @throws AddressLookupBadResponseException
     * @throws AddressLookupQuotaExceededException
     * @return mixed
     */
    protected function getSingleAddressHandleDTO($lookupStr) {
        $url = $this->constructApiURL($lookupStr, false);
//        $jsonResponseString = file_get_contents($url);
        $s = microtime(true);
        $jsonResponseString = $this->getJSONResponse($url);
        $e = microtime(true);
        echo "\nTime to get repsonse: " . ($e-$s) . "s\n";
        $responseObject = json_decode($jsonResponseString);
        $statusCode = $this->googleStatusCodeMapper[$responseObject->status];
        $dto = null;
        if ($statusCode === 1 || $statusCode === 2) {
            $results = $responseObject->results;
            //get only first result and quit
            if (count($results) >= 1) {
                $dto = $this->parseResponseToDTO($results[0]);
            }
        } elseif ($statusCode === 3) {
            throw new AddressLookupQuotaExceededException();
        } else {
            throw new AddressLookupBadResponseException();
        }
        return $dto;
    }

    /**
     * @return int
     */
    protected function getMaxDailyQuota() {
        return 2500;
    }

    /**
     * @param $responseObject
     * @return AddressHandleDTO
     */
    protected function parseResponseToDTO($responseObject) {
        $name = null;
        $streetNr = null;
        $streetName = null;
        $postalCode = null;
        $city = null;
        $country = null;
        $lat = null;
        $long = null;

        $name = $responseObject->formatted_address;
        $lat = GeometryService::serialize($responseObject->geometry->location->lat);
        $lng = GeometryService::serialize($responseObject->geometry->location->lng);

        $addressComponents = $responseObject->address_components;
        foreach ($addressComponents as $addressComponent) {
            $types = $addressComponent->types;
            if (in_array('street_number', $types)) {
                $streetNr = $this->getLongName($addressComponent);
            } elseif (in_array('route', $types)) {
                $streetName = $this->getLongName($addressComponent);
            } elseif (in_array('administrative_area_level_2', $types)) {
                $city = $this->getLongName($addressComponent);
            } elseif (in_array('administrative_area_level_1', $types)) {
                if (null === $city) {
                    $city = $this->getLongName($addressComponent);
                }
            } elseif (in_array('country', $types)) {
                $country = $this->getLongName($addressComponent);
            } elseif (in_array('postal_code', $types)) {
                $postalCode = $this->getLongName($addressComponent);
            }
        }

        $street = $streetName;
        if (null !== $streetNr) {
            $street .= ' ' . $streetNr;
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

    /**
     * @param $addressComponent
     * @return mixed
     */
    protected function getLongName($addressComponent) {
        return $addressComponent->long_name;
    }

    /**
     * @param $lookupStr
     * @param bool $withRegion
     * @return string
     */
    protected function constructApiURL($lookupStr, $withRegion = true) {
        /** @var ClientIdService $clientIdService */
        $clientIdService = $this->container->get('tixi_api.clientidservice');
        $uncheckedClientId = $this->container->getParameter('tixi_parameter_client');
        $googleApiKey = $this->container->getParameter('tixi_parameter_google_apikey');

        $clientId = $clientIdService->getClientId($uncheckedClientId);
        $keywords = $this->constructKeywordArray($lookupStr);
        $region = $this->constructRegion($clientId);
        $components = $this->constructComponents($clientId, $region, $keywords);

        $url = self::APIBASEURL . '?';
        $urlParameters = array();
        $keywordSection = $this->constructKeywordSection($keywords);
        if ('' !== $keywordSection) {
            $urlParameters[] = $keywordSection;
        }

        if ($withRegion) {
            $componentSection = $this->constructComponentsSection($components);
            if ('' !== $componentSection) {
                $urlParameters[] = $componentSection;
            }

            $regionSection = $this->constructRegionSection($region);
            if ('' !== $regionSection) {
                $urlParameters[] = $regionSection;
            }
        }

        $languageSection = $this->constructLanguageSection($region);
        if ('' !== $languageSection) {
            $urlParameters[] = $languageSection;
        }

        $apiKeySection = $this->constructApiKeySection($googleApiKey);
        if ('' !== $apiKeySection) {
            $urlParameters[] = $apiKeySection;
        }
        $urlParameters[] = $this->constructAdditionalSection();
        $urlParameterString = implode('&', $urlParameters);
        $urlParameterString = StringService::convertStringToASCII($urlParameterString);
        $url .= $urlParameterString;

//        $url = StringService::convertStringToASCII($url);
        return $url;
    }

    /**
     * @param $lookupStr
     * @return array
     */
    protected
    function constructKeywordArray($lookupStr) {
        return explode(' ', $lookupStr);
    }

    /**
     * @param array $keywordsArray
     * @return string
     */
    protected
    function constructKeywordSection(array $keywordsArray = array()) {
        $keywordsString = '';
        $keywords = implode('+', $keywordsArray);
        if ('' !== $keywords) {
            $keywordsString .= 'address=' . $keywords;
        }
        return $keywordsString;
    }

    /**
     * @param $clientId
     * @return string
     */
    protected
    function constructRegion($clientId) {
        $regionStr = '';
        if ($clientId === ClientIdService::ZUGID) {
            $regionStr = 'CH';
        }
        return $regionStr;
    }

    /**
     * @param string $region
     * @return string
     */
    protected
    function constructRegionSection($region = '') {
        $regionString = '';
        if ('' !== $region) {
            $regionString .= 'region=' . $region;
        }
        return $regionString;
    }

    /**
     * @param $clientId
     * @param $region
     * @param $keywords
     * @return array
     */
    protected
    function constructComponents($clientId, $region, $keywords) {
        $componentArray = array();
        if ($clientId === ClientIdService::ZUGID) {
            if (!$this->containsRegionInformation($region, $keywords)) {
                $componentArray[] = 'administrative_area:ZG';
            }
        }
        return $componentArray;
    }

    /**
     * @param array $componentsArray
     * @return string
     */
    protected
    function constructComponentsSection(array $componentsArray = array()) {
        $componentsString = '';
        $components = implode('|', $componentsArray);
        if ($components) {
            $componentsString .= 'components=' . $components;
        }
        return $componentsString;
    }

    /**
     * @return string
     */
    protected
    function constructAdditionalSection() {
        //ToDo remove userIp from final version and add server-ip as allowed ip in google credentials
        return 'sensor=false';
    }

    /**
     * @param string $apiKey
     * @return string
     */
    protected
    function constructApiKeySection($apiKey = '') {
        $apiKeyString = '';
        if ('' !== $apiKey) {
            $apiKeyString .= 'key=' . $apiKey;
        }
        return $apiKeyString;
    }

    /**
     * @param string $region
     * @return string
     */
    protected
    function constructLanguageSection($region = '') {
        $languageString = '';
        if ($region === 'CH') {
            $languageString .= 'language=de';
        }
        return $languageString;
    }

    /**
     * @param $region
     * @param $keywords
     * @return bool
     */
    protected
    function containsRegionInformation($region, $keywords) {
        $compareArray = array();
        if ($region === 'CH') {
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
        return (count(array_intersect($keywords, $compareArray)) > 0);
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function getJSONResponse($url) {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_DNS_USE_GLOBAL_CACHE => 1,
            CURLOPT_DNS_CACHE_TIMEOUT => 3600,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        );
        curl_setopt_array($curl, $options);
        $jsonResponseString = curl_exec($curl);
        curl_close($curl);
        return $jsonResponseString;
    }

}