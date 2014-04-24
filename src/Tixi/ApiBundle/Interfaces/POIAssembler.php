<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Interfaces;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\Address;

/**
 * Class POIAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class POIAssembler {
    /**
     * @param POIRegisterDTO $poiDTO
     * @throws \Exception
     * @return POI
     */
    public function registerDTOtoNewPOI(POIRegisterDTO $poiDTO) {
        $poi = POI::registerPOI($poiDTO->name,
            Address::registerAddress(
                $poiDTO->street, $poiDTO->postalCode,
                $poiDTO->city, $poiDTO->country, $poiDTO->address_name, $poiDTO->lat, $poiDTO->lng, $poiDTO->type),
            $poiDTO->department, $poiDTO->telephone, $poiDTO->comment, $poiDTO->memo, $poiDTO->details);
        foreach ($poiDTO->keywords as $keyword) {
            $poi->assignKeyword($keyword);
        }
        return $poi;
    }

    /**
     * @param POI $poi
     * @param POIRegisterDTO $poiDTO
     * @throws \Exception
     * @return POI
     */
    public function registerDTOToPOI(POIRegisterDTO $poiDTO, POI $poi) {
        $address = $poi->getAddress();
        $address->updateAddressData($poiDTO->street, $poiDTO->postalCode,
            $poiDTO->city, $poiDTO->country, $poiDTO->address_name, $poiDTO->lat, $poiDTO->lng, $poiDTO->type);
        $poi->updatePOIData($poiDTO->name, $address, $poiDTO->department,
            $poiDTO->telephone, $poiDTO->comment, $poiDTO->memo, $poiDTO->details);
        $poi->setKeywords($poiDTO->keywords);
        return $poi;
    }

    /**
     * @param POI $poi
     * @return POIRegisterDTO
     */
    public function poiToPOIRegisterDTO(POI $poi) {
        $poiDTO = new POIRegisterDTO();
        $poiDTO->id = $poi->getId();
        $poiDTO->isActive = $poi->getIsActive();
        $poiDTO->name = $poi->getName();
        $poiDTO->department = $poi->getDepartment();
        $poiDTO->telephone = $poi->getTelephone();
        $poiDTO->comment = $poi->getComment();
        $poiDTO->memo = $poi->getMemo();
        $poiDTO->details = $poi->getDetails();

        $poiDTO->keywords = $poi->getKeywords();

        $poiDTO->address_id = $poi->getAddress()->getId();
        $poiDTO->street = $poi->getAddress()->getStreet();
        $poiDTO->postalCode = $poi->getAddress()->getPostalCode();
        $poiDTO->city = $poi->getAddress()->getCity();
        $poiDTO->country = $poi->getAddress()->getCountry();
        $poiDTO->lat = $poi->getAddress()->getLat();
        $poiDTO->lng = $poi->getAddress()->getLng();

        return $poiDTO;
    }


    /**
     * @param $pois
     * @return array
     */
    public function poisToPOIListDTOs($pois) {
        $dtoArray = array();
        foreach ($pois as $poi) {
            $dtoArray[] = $this->poiToPOIListDTO($poi);
        }
        return $dtoArray;
    }

    /**
     * @param POI $poi
     * @return POIListDTO
     */
    public function poiToPOIListDTO(POI $poi) {
        $poiListDTO = new POIListDTO();
        $poiListDTO->id = $poi->getId();
        $poiListDTO->isActive = $poi->getIsActive();
        $poiListDTO->name = $poi->getName();
        $poiListDTO->department = $poi->getDepartment();
        $poiListDTO->telephone = $poi->getTelephone();
        $poiListDTO->street = $poi->getAddress()->getStreet();
        $poiListDTO->city = $poi->getAddress()->getCity();
        $poiListDTO->keywords = $poi->getKeywordsAsString();

        return $poiListDTO;
    }
}