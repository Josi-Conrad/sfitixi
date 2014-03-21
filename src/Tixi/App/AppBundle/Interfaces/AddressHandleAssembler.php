<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 21.03.14
 * Time: 14:59
 */

namespace Tixi\App\AppBundle\Interfaces;


use Tixi\CoreDomain\Address;

class AddressHandleAssembler {

    public static function toAddressHandleDTO(Address $address){
        $addressHandleDTO = new AddressHandleDTO();

        $addressHandleDTO->id = $address->getId();
        $addressHandleDTO->name = $address->getName();
        $addressHandleDTO->street = $address->getStreet();

        $addressHandleDTO->postalCode = $address->getPostalCode()->getCode();
        $addressHandleDTO->city = $address->getCity()->getName();
        $addressHandleDTO->country = $address->getCountry()->getName();

        $addressHandleDTO->lat = $address->getLat();
        $addressHandleDTO->lng = $address->getLng();
        $addressHandleDTO->type = $address->getType();

        return $addressHandleDTO;
    }

} 