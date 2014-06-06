<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

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

    public function registerDtoToNewDrivingOrders(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger) {
        $orders = [];
        $outwardOrder = $this->registerDtoToDrivingOrder($registerDTO, $passenger, self::OUTWARD_DIRECTION);
        $orders[] = $outwardOrder;
        if(null !== $registerDTO->orderTime->returnTime) {
            $returnOrder = $this->registerDtoToDrivingOrder($registerDTO, $passenger, self::RETURN_DIRECTION);
            $outwardOrder->assignReturnOrder($returnOrder);
            $orders[] = $returnOrder;
        }
        return $orders;
    }

    protected function registerDtoToDrivingOrder(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger, $direction) {
        $route = null;
        /** @var Address $fromAddress */
        $fromAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressFrom);
        /** @var Address $toAddress */
        $toAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressTo);
        /** @var Zone $zone */
        $zone = $this->zonePlanManagement->getZoneWithHighestPriorityForCities(array($fromAddress->getCity(), $toAddress->getCity()));
        if($direction===self::OUTWARD_DIRECTION) {
            $pickupTime = $registerDTO->orderTime->outwardTime;
            $route = $this->routeManagement->getRouteFromAddresses($fromAddress, $toAddress);
        }else {
            $pickupTime = $registerDTO->orderTime->returnTime;
            $route = $this->routeManagement->getRouteFromAddresses($toAddress, $fromAddress);
        }

        $drivingOrder = DrivingOrder::registerDrivingOrder(
            $passenger,
            $registerDTO->anchorDate,
            $pickupTime,
            $registerDTO->compagnion,
            $registerDTO->memo
        );
        $drivingOrder->assignRoute($route);
        if(null !== $zone) {
            $drivingOrder->assignZone($zone);
        }
        return $drivingOrder;
    }


    public function registerDtoToNewRepeatedDrivingOrder(DrivingOrderRegisterDTO $registerDTO) {

    }

    public function setAddressAssembler(AddressAssembler $assembler) {
        $this->addressAssembler = $assembler;
    }

    public function setRouteManagement(RouteManagement $routeManagement) {
        $this->routeManagement = $routeManagement;
    }

    public function setZonePlaneManagement(ZonePlanManagement $zonePlanManagement) {
        $this->zonePlanManagement = $zonePlanManagement;
    }
}