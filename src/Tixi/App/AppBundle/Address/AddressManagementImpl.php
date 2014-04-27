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
     * @return mixed
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO)
    {
        // TODO: Implement handleAddress() method.
    }
}