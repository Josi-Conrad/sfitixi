<?php

namespace Tixi\App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tixi\App\AddressManagement;
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\App\AppBundle\Interfaces\AddressHandleAssembler;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\City;
use Tixi\CoreDomain\PostalCode;
use Tixi\CoreDomain\Country;

class AddressManagementImpl extends Controller implements AddressManagement {
    /**
     * @param $addressString
     * @return AddressHandleDTO[]
     */
    public function getAddressSuggestionsByString($addressString) {
        $repository = $this->get('address_repository');
        $query = $repository->createQueryBuilder('a')
            ->where('a.name LIKE :word')
            ->orWhere('a.street LIKE :word')
            ->setParameter('word', '%'.$addressString.'%')
            ->getQuery();
        $results = $query->getResult();

        $addresses = array();

        /** @var $res Address */
        foreach($results as $res){
            $addresses[] = AddressHandleAssembler::toAddressHandleDTO($res);
        }

        return $addresses;
    }

    /**
     * @param AddressHandleDTO $addressHandleDTO
     * @return Address
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO) {
        //Address exists already
        if (!empty($addressHandleDTO->id)) {
            //existing Address changed
            if ($addressHandleDTO->editFlag) {
                $address = $this->get('address_repository')->find($addressHandleDTO->id);
                $address->updateAddressBasicData(
                    $addressHandleDTO->name,
                    $addressHandleDTO->street,
                    $this->getOrCreatePostalCode($addressHandleDTO->postalCode),
                    $this->getOrCreateCity($addressHandleDTO->city),
                    $this->getOrCreateCountry($addressHandleDTO->country),
                    $addressHandleDTO->lat,
                    $addressHandleDTO->lng,
                    $addressHandleDTO->type
                );
                $this->get('address_repository')->store($address);
                return $address;
            }
        } //New Address
        else {
            $address = Address::registerAddress(
                $addressHandleDTO->name,
                $addressHandleDTO->street,
                $this->getOrCreatePostalCode($addressHandleDTO->postalCode),
                $this->getOrCreateCity($addressHandleDTO->city),
                $this->getOrCreateCountry($addressHandleDTO->country),
                $addressHandleDTO->lat,
                $addressHandleDTO->lng,
                $addressHandleDTO->type
            );
            $this->get('address_repository')->store($address);
            return $address;
        }
    }

    /**
     * @param $postalCode
     * @return PostalCode
     */
    private function getOrCreatePostalCode($postalCode) {
        $current = $this->get('postal_code_repository')->findOneBy(array('code' => $postalCode));
        if (empty($current)) {
            $postalCode = PostalCode::registerPostalCode($postalCode);
            $this->get('postal_code_repository')->store($postalCode);
            return $postalCode;
        }
        return $current;
    }

    /**
     * @param $cityName
     * @return City
     */
    private function getOrCreateCity($cityName) {
        $current = $this->get('city_repository')->findOneBy(array('name' => $cityName));
        if (empty($current)) {
            $city = City::registerCity($cityName);
            $this->get('city_repository')->store($city);
            return $city;
        }
        return $current;
    }

    /**
     * @param $countryName
     * @return Country
     */
    private function getOrCreateCountry($countryName) {
        $current = $this->get('country_repository')->findOneBy(array('name' => $countryName));
        if (empty($current)) {
            $country = Country::registerCountry($countryName);
            $this->get('country_repository')->store($country);
            return $country;
        }
        return $current;
    }
}