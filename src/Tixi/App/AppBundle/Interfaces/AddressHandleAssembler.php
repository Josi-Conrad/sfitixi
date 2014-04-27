<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 21.03.14
 * Time: 14:59
 */

namespace Tixi\App\AppBundle\Interfaces;


use Tixi\CoreDomain\Address;

/**
 * Class AddressHandleAssembler
 * @package Tixi\App\AppBundle\Interfaces
 */
class AddressHandleAssembler {

    /**
     * @param Address $address
     * @return AddressHandleDTO
     */
    public static function toAddressHandleDTO(Address $address){
        $addressHandleDTO = new AddressHandleDTO();

        $addressHandleDTO->id = $address->getId();
        $addressHandleDTO->name = $address->getName();
        $addressHandleDTO->street = $address->getStreet();
        $addressHandleDTO->postalCode = $address->getPostalCode();
        $addressHandleDTO->city = $address->getCity();
        $addressHandleDTO->country = $address->getCountry();

        $addressHandleDTO->lat = $address->getLat();
        $addressHandleDTO->lng = $address->getLng();
        $addressHandleDTO->source = $address->getSource();

        return $addressHandleDTO;
    }

} 