<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 10:28
 */

namespace Tixi\App\AppBundle\Address;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Address\AddressManagement;
use Tixi\App\AppBundle\Interfaces\AddressHandleAssembler;
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine;

/**
 * Class AddressManagementImpl
 * @package Tixi\App\AppBundle\Address
 */
class AddressManagementImpl extends ContainerAware implements AddressManagement {

    /**
     * Returns Address Object Suggestions from a string input (like google search)
     *
     * @param $addressString
     * @return AddressHandleDTO[]
     */
    public function getAddressSuggestionsByString($addressString) {
        $serviceTrail = $this->createServiceTrail();
        $addressSuggestions = array();
        /** @var AddressLookupService $service */
        foreach ($serviceTrail as $service) {
            try {
                $addressSuggestions = $service->lookup($addressString);
                if (count($addressSuggestions) !== 0) {
                    break;
                }
            } catch (\Exception $e) {
                //skip service
            }
        }
        return $addressSuggestions;
    }

    /**
     * Get array of AddressHandleDTOs with size of one containing the users home address associated with the given
     * user id
     *
     * @param $passengerId
     * @return mixed
     */
    public function getAddressHandleByPassengerId($passengerId)
    {
        $handleDtos = array();
        $passengerRepository = $this->container->get('passenger_repository');
        $passenger = $passengerRepository->find($passengerId);
        if($passenger) {
            $handleDtos[] = AddressHandleAssembler::toAddressHandleDTO($passenger->getAddress());
        }
        return $handleDtos;
    }

    /**
     * creates serviceTrail according to registered lookup services
     * @return array
     */
    protected function createServiceTrail() {
        $lookupServiceFactory = $this->container->get('tixi_app.addresslookupfactory');
        $serviceTrail = array();
        $serviceTrail[] = $lookupServiceFactory->get(AddressLookupFactory::$localAddressServiceId);
        $serviceTrail[] = $lookupServiceFactory->get(AddressLookupFactory::$localPoiServiceId);
        $serviceTrail[] = $lookupServiceFactory->get(AddressLookupFactory::$googleServiceId);
        return $serviceTrail;

    }

    /**
     * Will query  AddressString on a lookup service like google and takes first best suggestion given.
     * Addresstring should be valid for exact queries. Returns Suggestion as an AddressHandleDTO
     *
     * @param $addressString
     * @return AddressHandleDTO
     */
    public function getAddressInformationByString($addressString) {
        //set lookup service, could possibly be another implementation
        $service = $this->container->get('tixi_app.addresslookupfactory')->get(AddressLookupFactory::$googleServiceId);
        $addressInformation = null;
        try {
            $addressInformation = $service->lookupSingleAddress($addressString);
        } catch (\Exception $e) {

        }
        return $addressInformation;
    }

    /**
     * Handles a new Address object if register new one or get an existing one
     *
     * @param AddressHandleDTO $addressHandleDTO
     * @return mixed|void
     * @throws \Exception
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO) {
        /** @var AddressRepositoryDoctrine $addressRepository */
        $addressRepository = $this->container->get('address_repository');
        $address = null;
        if (null === $addressHandleDTO->id) {
            //create new address
            $address = Address::registerAddress(
                $addressHandleDTO->street,
                $addressHandleDTO->postalCode,
                $addressHandleDTO->city,
                $addressHandleDTO->country,
                null,
                $addressHandleDTO->lat,
                $addressHandleDTO->lng,
                $addressHandleDTO->source
            );
            $addressRepository->store($address);
        } else {
            /** @var Address $address */
            $address = $addressRepository->find($addressHandleDTO->id);
            if (null === $address) {
                throw new \Exception('The address with id ' . $addressHandleDTO->id . ' does not exist');
            }
            if ($addressHandleDTO->source === Address::SOURCE_MANUAL) {
                $address->updateAddressData(
                    $addressHandleDTO->street,
                    $addressHandleDTO->postalCode,
                    $addressHandleDTO->city,
                    $addressHandleDTO->country,
                    null,
                    $addressHandleDTO->lat,
                    $addressHandleDTO->lng,
                    $addressHandleDTO->source
                );
            }
        }
        return $address;
    }

}