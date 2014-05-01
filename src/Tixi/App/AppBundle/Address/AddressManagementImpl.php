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
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine;

class AddressManagementImpl extends ContainerAware implements AddressManagement{

    /**
     * Returns Address Object Suggestions from a string input (like google search)
     *
     * @param $addressString
     * @return AddressHandleDTO
     */
    public function getAddressSuggestionsByString($addressString)
    {
        $serviceTrail = $this->createServiceTrail();
        $addressSuggestions = array();
        /** @var AddressLookupService $service */
        foreach($serviceTrail as $service) {
            try{
                $addressSuggestions = $service->lookup($addressString);
                if(count($addressSuggestions) !== 0) {
                    break;
                }
            }catch (\Exception $e) {

            }
        }
        return $addressSuggestions;
    }

    protected function createServiceTrail() {
        $lookupServiceFactory = $this->container->get('tixi_app.addresslookupfactory');
        $serviceTrail = array();
        $serviceTrail[] = $lookupServiceFactory->get(AddressLookupFactory::$localServiceId);
        $serviceTrail[] = $lookupServiceFactory->get(AddressLookupFactory::$googleServiceId);
        return $serviceTrail;

    }

    /**
     * Handles a new Address object if register new one or get an existing one
     *
     * @param AddressHandleDTO $addressHandleDTO
     * @return mixed|void
     * @throws \Exception
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO)
    {
        /** @var AddressRepositoryDoctrine $addressRepository */
        $addressRepository = $this->get('address_repository');
        if(null === $addressHandleDTO->id) {
            //create new address
            $address = Address::registerAddress(
                $addressHandleDTO->street,
                $addressHandleDTO->postalCode,
                $addressHandleDTO->city,
                $addressHandleDTO->country,
                $addressHandleDTO->name,
                $addressHandleDTO->lat,
                $addressHandleDTO->lng,
                $addressHandleDTO->source
            );
            $addressRepository->store($address);
        }else {
            if($addressHandleDTO->source===Address::SOURCE_MANUAL) {
                /** @var Address $address */
                $address = $addressRepository->find($addressHandleDTO->id);
                if(null !== $address) {
                    $address->updateAddressData(
                        $addressHandleDTO->street,
                        $addressHandleDTO->postalCode,
                        $addressHandleDTO->city,
                        $addressHandleDTO->country,
                        $addressHandleDTO->name,
                        $addressHandleDTO->lat,
                        $addressHandleDTO->lng,
                        $addressHandleDTO->source
                    );
                }else {
                    throw new \Exception('The address with id ' . $addressHandleDTO->id . ' does not exist');
                }

            }
        }
    }
}