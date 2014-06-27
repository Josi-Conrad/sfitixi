<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\ApiBundle\Form\Dispo\DrivingOrderOutwardTimeException;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderReturnTimeException;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Interfaces\AddressAssembler;
use Tixi\App\Routing\RouteManagement;
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Zone;

/**
 * Class DrivingOrderAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class DrivingOrderAssembler {

    const OUTWARD_DIRECTION = 'out';
    const RETURN_DIRECTION = 'return';

    /** @var  AddressAssembler $addressAssembler */
    protected $addressAssembler;
    /** @var  RouteManagement $routeManagement */
    protected $routeManagement;
    /** @var  ZonePlanManagement $zonePlanManagement */
    protected $zonePlanManagement;
    /** @var $dateTimeService DateTimeService */
    protected $dateTimeService;

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @param Passenger $passenger
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return array
     */
    public function registerDtoToNewDrivingOrders(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger) {
        $orders = [];
        try {
            $outwardOrder = $this->registerDtoToDrivingOrder($registerDTO, $passenger, self::OUTWARD_DIRECTION);
        }catch (DrivingOrderOutwardTimeException $e) {
            throw $e;
        }
        $orders[] = $outwardOrder;
        if(null !== $registerDTO->orderTime->returnTime) {
            if(empty($registerDTO->orderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            try {
                $returnOrder = $this->registerDtoToDrivingOrder($registerDTO, $passenger, self::RETURN_DIRECTION);
            }catch (DrivingOrderReturnTimeException $e) {
                throw $e;
            }
            $outwardOrder->assignReturnOrder($returnOrder);
            $orders[] = $returnOrder;
        }
        return $orders;
    }

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @param Passenger $passenger
     * @param $direction
     * @return DrivingOrder
     * @throws \Tixi\ApiBundle\Form\Dispo\DrivingOrderReturnTimeException
     * @throws \Tixi\ApiBundle\Form\Dispo\DrivingOrderOutwardTimeException
     */
    protected function registerDtoToDrivingOrder(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger, $direction) {
        $route = null;
        /** @var Address $fromAddress */
        $fromAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressFrom);
        /** @var Address $toAddress */
        $toAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressTo);
        /** @var Zone $zone */
        $zone = $this->zonePlanManagement->getZoneWithHighestPriorityForCities(array($fromAddress->getCity(), $toAddress->getCity()));
        if($direction===self::OUTWARD_DIRECTION) {
            if(null === $registerDTO->orderTime) {
                throw new DrivingOrderOutwardTimeException();
            }
            $pickupTime = $registerDTO->orderTime->outwardTime;
            $route = $this->routeManagement->getRouteFromAddresses($fromAddress, $toAddress);
        }else {
            if(null === $registerDTO->orderTime) {
                throw new DrivingOrderReturnTimeException();
            }
            $pickupTime = $registerDTO->orderTime->returnTime;
            $route = $this->routeManagement->getRouteFromAddresses($toAddress, $fromAddress);
        }
        $drivingOrder = DrivingOrder::registerDrivingOrder(
            $passenger,
            $registerDTO->anchorDate,
            $pickupTime,
            $registerDTO->compagnion,
            $registerDTO->memo,
            DrivingOrder::PENDENT,
            false,
            $registerDTO->additionalTime
        );
        $drivingOrder->assignRoute($route);
        if(null !== $zone) {
            $drivingOrder->assignZone($zone);
        }
        $passenger->assignDrivingOrder($drivingOrder);
        return $drivingOrder;
    }

    /**
     * @param DrivingOrderEditDTO $editDTO
     * @param DrivingOrder $drivingOrder
     */
    public function editDTOtoDrivingOrder(DrivingOrderEditDTO $editDTO, DrivingOrder $drivingOrder) {
        $drivingOrder->update(
            $editDTO->memo,
            $editDTO->orderStatus
        );
    }

    /**
     * @param DrivingOrder $drivingOrder
     * @return DrivingOrderEditDTO
     */
    public function drivingOrderToEditDto(DrivingOrder $drivingOrder) {
        $dto = new DrivingOrderEditDTO();
        $dto->id = $drivingOrder->getId();
        $dto->pickupDate = $this->dateTimeService->convertToLocalDateTime($drivingOrder->getPickUpDate())->format('d.m.Y');
        $dto->pickupTime = $this->dateTimeService->convertToLocalDateTime($drivingOrder->getPickUpTime())->format('H:i');
        $dto->lookaheadaddressFrom = $drivingOrder->getRoute()->getStartAddress()->toString();
        $dto->lookaheadaddressTo = $drivingOrder->getRoute()->getTargetAddress()->toString();
        $dto->zoneName = $drivingOrder->getZone()->getName();
        $dto->compagnion = $drivingOrder->getCompanion();
        $dto->memo = $drivingOrder->getMemo();
        $dto->additionalTime = $drivingOrder->getAdditionalTime();
        $dto->orderStatus = $drivingOrder->getStatus();
        return $dto;
    }

    /**
     * @param $drivingOrders
     * @return array
     */
    public function drivingOrdersToDrivingOrderEmbeddedListDTOs($drivingOrders) {
        $dtoArray = array();
        foreach ($drivingOrders as $drivingOrder) {
            $dtoArray[] = $this->drivingOrderToDrivingOrderEmbeddedListDTO($drivingOrder);
        }
        return $dtoArray;
    }

    /**
     * @param DrivingOrder $drivingOrder
     * @return DrivingOrderEmbeddedListDTO
     */
    public function drivingOrderToDrivingOrderEmbeddedListDTO(DrivingOrder $drivingOrder) {
        $listDTO = new DrivingOrderEmbeddedListDTO();
        $listDTO->id = $drivingOrder->getId();
        $listDTO->passengerId = $drivingOrder->getPassenger()->getId();
        $listDTO->pickupDate = $this->dateTimeService->convertToLocalDateTime($drivingOrder->getPickUpDate())->format('d.m.Y');
        $listDTO->pickupTime = $this->dateTimeService->convertToLocalDateTime($drivingOrder->getPickUpTime())->format('H:i');
        $listDTO->addressFromString = $drivingOrder->getRoute()->getStartAddress()->toString();
        $listDTO->addressToString = $drivingOrder->getRoute()->getTargetAddress()->toString();
        return $listDTO;
    }

    /**
     * @param AddressAssembler $assembler
     */
    public function setAddressAssembler(AddressAssembler $assembler) {
        $this->addressAssembler = $assembler;
    }

    /**
     * @param RouteManagement $routeManagement
     */
    public function setRouteManagement(RouteManagement $routeManagement) {
        $this->routeManagement = $routeManagement;
    }

    /**
     * @param ZonePlanManagement $zonePlanManagement
     */
    public function setZonePlaneManagement(ZonePlanManagement $zonePlanManagement) {
        $this->zonePlanManagement = $zonePlanManagement;
    }

    /**
     * @param $dateTimeService
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }
}