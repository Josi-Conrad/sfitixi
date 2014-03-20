<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.03.14
 * Time: 16:53
 */

namespace Tixi\ApiBundle\Interfaces;


use Tixi\CoreDomain\Vehicle;

class VehicleAssembler {

    public static function toVehicleRegisterDTO(Vehicle $vehicle) {
        $vehicleDTO = new VehicleRegisterDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->name= $vehicle->getName();
        $vehicleDTO->licenceNumber = $vehicle->getLicenceNumber();
        $vehicleDTO->dateOfFirstRegistration = $vehicle->getDateOfFirstRegistration();
        $vehicleDTO->parkingLotNumber = $vehicle->getParkingLotNumber();
        $vehicleDTO->vehicleCategory = $vehicle->getCategory();
        return $vehicleDTO;
    }

    public static function vehiclesToVehicleListDTOs($vehicles) {
        $dtoArray = array();
        foreach($vehicles as $vehicle) {
            $dtoArray[] = self::toVehicleListDTO($vehicle);
        }
        return $dtoArray;
    }

    public static function toVehicleListDTO(Vehicle $vehicle) {
        $vehicleListDTO = new VehicleListDTO();
        $vehicleListDTO->id = $vehicle->getId();
        $vehicleListDTO->name = $vehicle->getName();
        $vehicleListDTO->licenceNumber = $vehicle->getLicenceNumber();
        return $vehicleListDTO;
    }

} 