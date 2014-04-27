<?php

namespace Tixi\App\AppBundle\Controller;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tixi\App\Address\AddressManagement;
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\App\AppBundle\Interfaces\AddressHandleAssembler;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\City;
use Tixi\CoreDomain\PostalCode;
use Tixi\CoreDomain\Country;

/**
 * Class AddressManagementImplDoctrine
 * @package Tixi\App\AppBundle\Controller
 */
class AddressManagementImplDoctrine extends Controller implements AddressManagement {
    /**
     * @param $addressString
     * @param int $limit
     * @return AddressHandleDTO[]
     */
    public function getAddressSuggestionsByString($addressString, $limit = 5) {

        $searchString = $this->tokenizeFulltextSearchString($addressString);

        //native query, no FTS function in DBAL
        $em = $this->get('entity_manager');
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('Tixi\CoreDomain\Address', 'a');

        $sql = "SELECT a.id, a.street, a.postalCode, a.city, a.country, a.lat, a.lng, a.type FROM address a
        WHERE MATCH (name, street, postalCode, city, country, type)
        AGAINST ('.$searchString.' IN BOOLEAN MODE)
        LIMIT 0, " . $limit;

        $query = $em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        $addresses = array();
        /** @var $result Address */
        foreach ($results as $result) {
            $addresses[] = AddressHandleAssembler::toAddressHandleDTO($result);
        }

        return $addresses;
    }

    /**
     * @param AddressHandleDTO $addressHandleDTO
     * @return Address
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO) {

        /**
         * Manually edited address -> call google service to get correct values
         * (if we are online)
         */
        if ($addressHandleDTO->editFlag) {

        }

        //Address exists already
        if (!empty($addressHandleDTO->id)) {
            /** @var Address $address */
            $address = $this->get('address_repository')->find($addressHandleDTO->id);
            $address->updateAddressData(
                $addressHandleDTO->name,
                $addressHandleDTO->street,
                $addressHandleDTO->postalCode,
                $addressHandleDTO->city,
                $addressHandleDTO->country,
                $addressHandleDTO->lat,
                $addressHandleDTO->lng,
                $addressHandleDTO->type,
                $addressHandleDTO->editFlag
            );
            $this->get('address_repository')->store($address);
            return $address;
        } else { //New Address
            /** @var Address $address */
            $address = Address::registerAddress(
                $addressHandleDTO->name,
                $addressHandleDTO->street,
                $addressHandleDTO->postalCode,
                $addressHandleDTO->city,
                $addressHandleDTO->country,
                $addressHandleDTO->lat,
                $addressHandleDTO->lng,
                $addressHandleDTO->type,
                $addressHandleDTO->editFlag
            );
            $this->get('address_repository')->store($address);
            return $address;
        }
    }

    /**
     * @param $addressString
     * @return string
     */
    private function tokenizeFulltextSearchString($addressString) {
        $searchString = '';
        $words = explode(' ', $addressString);

        foreach ($words as $word) {
            $searchString .= '+' . $word . '* ';
        }

        return $searchString;
    }
}