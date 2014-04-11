<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.03.14
 * Time: 16:53
 */

namespace Tixi\ApiBundle\Interfaces;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tixi\CoreDomain\Vehicle;

class VehicleAssembler {
    /**
     * @param VehicleRegisterDTO $vehicleDTO
     * @return Vehicle
     * @throws \Exception
     */
    public function registerDTOtoNewVehicle(VehicleRegisterDTO $vehicleDTO) {
        $vehicle = Vehicle::registerVehicle($vehicleDTO->name, $vehicleDTO->licenceNumber,
            $vehicleDTO->dateOfFirstRegistration, $vehicleDTO->parking, $vehicleDTO->category,
            $vehicleDTO->memo, $vehicleDTO->managementDetails);
        if (!empty($vehicleDTO->supervisor)) {
            $vehicle->assignSupervisor($vehicleDTO->supervisor);
        }
        return $vehicle;
    }

    /**
     * @param Vehicle $vehicle
     * @param VehicleRegisterDTO $vehicleDTO
     * @throws \Exception
     */
    public function registerDTOToVehicle(Vehicle $vehicle, VehicleRegisterDTO $vehicleDTO) {
        $vehicle->updateVehicleData($vehicleDTO->name, $vehicleDTO->licenceNumber,
            $vehicleDTO->dateOfFirstRegistration, $vehicleDTO->parking, $vehicleDTO->category,
            $vehicleDTO->memo, $vehicleDTO->managementDetails);
        if (!empty($vehicleDTO->supervisor)) {
            $vehicle->assignSupervisor($vehicleDTO->supervisor);
        } else {
            $vehicle->removeSupervisor();
        }
    }

    /**
     * @param Vehicle $vehicle
     * @return VehicleRegisterDTO
     */
    public function toVehicleRegisterDTO(Vehicle $vehicle) {
        $vehicleDTO = new VehicleRegisterDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->name = $vehicle->getName();
        $vehicleDTO->licenceNumber = $vehicle->getLicenceNumber();
        $vehicleDTO->dateOfFirstRegistration = $vehicle->getDateOfFirstRegistration();
        $vehicleDTO->parking = $vehicle->getParking();
        $vehicleDTO->category = $vehicle->getCategory();
        $vehicleDTO->memo = $vehicle->getMemo();
        $vehicleDTO->managementDetails = $vehicle->getManagementDetails();
        $vehicleDTO->supervisor = $vehicle->getSupervisor();
        return $vehicleDTO;
    }

    /**
     * @param $vehicles
     * @return array
     */
    public function vehiclesToVehicleListDTOs($vehicles) {
        $dtoArray = array();
        foreach ($vehicles as $vehicle) {
            $dtoArray[] = $this->toVehicleListDTO($vehicle);
        }
        return $dtoArray;
    }

    /**
     * @param Vehicle $vehicle
     * @return VehicleListDTO
     */
    public function toVehicleListDTO(Vehicle $vehicle) {
        $vehicleListDTO = new VehicleListDTO();
        $vehicleListDTO->id = $vehicle->getId();
        $vehicleListDTO->name = $vehicle->getName();
        $vehicleListDTO->licenceNumber = $vehicle->getLicenceNumber();
        $vehicleListDTO->parking = $vehicle->getParking();
        $vehicleListDTO->dateOfFirstRegistration = $vehicle->getDateOfFirstRegistration()->format('d.m.Y');
        $vehicleListDTO->category = $vehicle->getCategory()->getName();
        $vehicleListDTO->amountOfSeats = $vehicle->getCategory()->getAmountOfSeats();
        $vehicleListDTO->amountOfWheelChairs = $vehicle->getCategory()->getAmountOfWheelChairs();
        return $vehicleListDTO;
    }
}