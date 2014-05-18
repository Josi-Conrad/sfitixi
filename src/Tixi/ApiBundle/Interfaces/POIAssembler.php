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

    /** @var  AddressAssembler $addressAssembler */
    protected $addressAssembler;

    /**
     * @param POIRegisterDTO $poiDTO
     * @throws \Exception
     * @return POI
     */
    public function registerDTOtoNewPOI(POIRegisterDTO $poiDTO) {
        $address = $this->addressAssembler->addressLookaheadDTOtoAddress($poiDTO->address);
        $poi = POI::registerPOI($poiDTO->name, $address, $poiDTO->department, $poiDTO->telephone,
            $poiDTO->comment, $poiDTO->details);
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
        $address = $this->addressAssembler->addressLookaheadDTOtoAddress($poiDTO->address);
        $poi->updatePOIData($poiDTO->name, $address, $poiDTO->department,
            $poiDTO->telephone, $poiDTO->comment, $poiDTO->details);
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
        $poiDTO->details = $poi->getDetails();

        $poiDTO->keywords = $poi->getKeywords();

        $poiDTO->address = $this->addressAssembler->addressToAddressLookaheadDTO($poi->getAddress());

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

    public function setAddressAssembler(AddressAssembler $assembler) {
        $this->addressAssembler = $assembler;
    }
}