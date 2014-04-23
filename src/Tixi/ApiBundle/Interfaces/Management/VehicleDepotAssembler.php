<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:48
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\VehicleDepot;

/**
 * Class VehicleDepotAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class VehicleDepotAssembler {
    /**
     * @param VehicleDepotRegisterDTO $dto
     * @return VehicleDepot
     */
    public function registerDTOtoNewVehicleDepot(VehicleDepotRegisterDTO $dto) {
        $vehicleDepot = VehicleDepot::registerVehicleDepot($dto->name,
            Address::registerAddress(
                $dto->street, $dto->postalCode,
                $dto->city, $dto->country));
        return $vehicleDepot;
    }

    /**
     * @param VehicleDepot $vehicleDepot
     * @param VehicleDepotRegisterDTO $dto
     */
    public function registerDTOtoVehicleDepot(VehicleDepot $vehicleDepot, VehicleDepotRegisterDTO $dto) {
        $address = $vehicleDepot->getAddress();
        $address->updateAddressData($dto->street, $dto->postalCode,
            $dto->city, $dto->country);
        $vehicleDepot->updateVehicleDepotData($dto->name);
    }

    /**
     * @param VehicleDepot $vehicleDepot
     * @return VehicleDepotRegisterDTO
     */
    public function toVehicleDepotRegisterDTO(VehicleDepot $vehicleDepot) {
        $vehicleDepotDTO = new VehicleDepotRegisterDTO();
        $vehicleDepotDTO->id = $vehicleDepot->getId();
        $vehicleDepotDTO->name = $vehicleDepot->getName();

        $vehicleDepotDTO->street = $vehicleDepot->getAddress()->getStreet();
        $vehicleDepotDTO->postalCode = $vehicleDepot->getAddress()->getPostalCode();
        $vehicleDepotDTO->city = $vehicleDepot->getAddress()->getCity();
        $vehicleDepotDTO->country = $vehicleDepot->getAddress()->getCountry();

        return $vehicleDepotDTO;
    }

    /**
     * @param $vehicleDepots
     * @return array
     */
    public function vehicleDepotsToVehicleDepotListDTOs($vehicleDepots) {
        $dtoArray = array();
        foreach ($vehicleDepots as $vehicleDepot) {
            $dtoArray[] = $this->toVehicleDepotListDTO($vehicleDepot);
        }
        return $dtoArray;
    }

    /**
     * @param VehicleDepot $vehicleDepot
     * @return VehicleDepotListDTO
     */
    public function toVehicleDepotListDTO(VehicleDepot $vehicleDepot) {
        $vehicleDepotListDTO = new VehicleDepotListDTO();
        $vehicleDepotListDTO->id = $vehicleDepot->getId();
        $vehicleDepotListDTO->name = $vehicleDepot->getName();

        $vehicleDepotListDTO->street = $vehicleDepot->getAddress()->getStreet();
        $vehicleDepotListDTO->city = $vehicleDepot->getAddress()->getCity();

        return $vehicleDepotListDTO;
    }
} 