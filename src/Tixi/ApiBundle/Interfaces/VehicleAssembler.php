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
        $dateOfFirstRegistration = $vehicleDTO->dateOfFirstRegistration;
        if (!$dateOfFirstRegistration) {
            throw new \Exception('bade date format detected');
        }
        $vehicle = Vehicle::registerVehicle($vehicleDTO->name, $vehicleDTO->licenceNumber,
            $dateOfFirstRegistration, $vehicleDTO->parkingLotNumber, $vehicleDTO->category,
            $vehicleDTO->memo, $vehicleDTO->managementDetails);
        $vehicle->assignSupervisor($vehicleDTO->supervisor);
        return $vehicle;
    }

    /**
     * @param Vehicle $vehicle
     * @param VehicleRegisterDTO $vehicleDTO
     * @throws \Exception
     */
    public function registerDTOToVehicle(Vehicle $vehicle, VehicleRegisterDTO $vehicleDTO) {
        $dateOfFirstRegistration = $vehicleDTO->dateOfFirstRegistration;
        if (!$dateOfFirstRegistration) {
            throw new \Exception('bad date format detected');
        }
        $vehicle->updateBasicData($vehicleDTO->name, $vehicleDTO->licenceNumber,
            $dateOfFirstRegistration, $vehicleDTO->parkingLotNumber, $vehicleDTO->category,
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
        $vehicleDTO->parkingLotNumber = $vehicle->getParkingLotNumber();
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
        ;
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
        $vehicleListDTO->parkingLotNumber = $vehicle->getParkingLotNumber();
        $vehicleListDTO->dateOfFirstRegistration = $vehicle->getDateOfFirstRegistration()->format('d.m.Y');
        $vehicleListDTO->category = $vehicle->getCategory()->getName();
        return $vehicleListDTO;
    }
}