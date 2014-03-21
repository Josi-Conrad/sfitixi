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
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Vehicle;

class VehicleAssembler{

    private $dateTimeService;

    public function toVehicleRegisterDTO(Vehicle $vehicle) {
        $vehicleDTO = new VehicleRegisterDTO();
        $vehicleDTO->id = $vehicle->getId();
        $vehicleDTO->name= $vehicle->getName();
        $vehicleDTO->licenceNumber = $vehicle->getLicenceNumber();
        $vehicleDTO->dateOfFirstRegistration = $this->dateTimeService->convertUTCDateToLocalString($vehicle->getDateOfFirstRegistration());
        $vehicleDTO->parkingLotNumber = $vehicle->getParkingLotNumber();
        $vehicleDTO->vehicleCategory = $vehicle->getCategory();
        return $vehicleDTO;
    }

    public function vehiclesToVehicleListDTOs($vehicles) {        ;
        $dtoArray = array();
        foreach($vehicles as $vehicle) {
            $dtoArray[] = $this->toVehicleListDTO($vehicle);
        }
        return $dtoArray;
    }

    public function toVehicleListDTO(Vehicle $vehicle) {
        $vehicleListDTO = new VehicleListDTO();
        $vehicleListDTO->id = $vehicle->getId();
        $vehicleListDTO->name = $vehicle->getName();
        $vehicleListDTO->licenceNumber = $vehicle->getLicenceNumber();
        $vehicleListDTO->parkingLot = $vehicle->getParkingLotNumber();
        $vehicleListDTO->dateOfFirstRegistration = $this->dateTimeService->convertUTCDateToLocalString($vehicle->getDateOfFirstRegistration());
        $vehicleListDTO->category = $vehicle->getCategory()->getName();
        return $vehicleListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }
}