<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.05.14
 * Time: 12:35
 */

namespace Tixi\ApiBundle\Interfaces;


use Tixi\App\Address\AddressManagement;
use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;
use Tixi\CoreDomain\Address;

class AddressAssembler {

    /** @var  AddressManagement $addressService */
    protected $addressService;

    public function addressToAddressLookaheadDTO(Address $address) {
        $lookahaedDTO = new AddressLookaheadDTO();
        $lookahaedDTO->addressSelectionId = 0;
        $lookahaedDTO->addressDisplayName = $address->toString();
        $lookahaedDTO->addressHandles = array();
        $lookahaedDTO->addressHandles[] = $this->createAddressHandleDTO($address);
        return $lookahaedDTO;
    }

    public function addressLookaheadDTOtoAddress(AddressLookaheadDTO $dto) {
        $address = null;
        if(isset($dto->addressSelectionId) && isset($dto->addressHandles)) {
            try {
                $address = $this->addressService->handleAddress($dto->addressHandles[$dto->addressSelectionId]);
            }catch (\Exception $e) {

            }
        }
        return $address;
    }

    protected function createAddressHandleDTO(Address $address) {
        $handleDTO = new AddressHandleDTO();
        $handleDTO->id = $address->getId();
        $handleDTO->street = $address->getStreet();
        $handleDTO->postalCode = $address->getPostalCode();
        $handleDTO->city = $address->getCity();
        $handleDTO->country = $address->getCountry();
        $handleDTO->lat = $address->getLat();
        $handleDTO->lng = $address->getLng();
        $handleDTO->source = $address->getSource();
        return $handleDTO;
    }

    public function setAddressService(AddressManagement $addressManagement) {
        $this->addressService = $addressManagement;
    }

} 