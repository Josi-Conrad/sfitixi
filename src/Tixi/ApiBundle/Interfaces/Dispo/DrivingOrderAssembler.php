<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\Route;

/**
 * Class DrivingOrderAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class DrivingOrderAssembler {





//    /**
//     * @param DrivingOrderRegisterDTO $drivingOrderDTO
//     * @return DrivingOrder
//     */
//    public function registerDTOtoNewDrivingOrder(DrivingOrderRegisterDTO $drivingOrderDTO) {
//        $drivingOrder = DrivingOrder::registerDrivingOrder(
//            $drivingOrderDTO->pickupDate,
//            $drivingOrderDTO->pickupTime,
//            $drivingOrderDTO->companion,
//            $drivingOrderDTO->memo
//        );
//
//        $route = Route::registerRoute($drivingOrderDTO->addressFrom,$drivingOrderDTO->addressTo,
//            $drivingOrderDTO->routeDuration,$drivingOrderDTO->routeDistance,$drivingOrderDTO->routeAdditionalTime);
//
//        $drivingOrder->assignRoute($route);
//
//        return $drivingOrder;
//    }
//
//    /**
//     * @param DrivingOrderRegisterDTO $drivingOrderDTO
//     * @param DrivingOrder $drivingOrder
//     * @return DrivingOrder
//     */
//    public function registerDTOtoDrivingOrder(DrivingOrderRegisterDTO $drivingOrderDTO, DrivingOrder $drivingOrder) {
//
//        return $drivingOrder;
//    }
//
//    /**
//     * @param DrivingOrder $drivingOrder
//     * @return DrivingOrderRegisterDTO
//     */
//    public function drivingOrderToDrivingOrderRegisterDTO(DrivingOrder $drivingOrder) {
//        $drivingOrderDTO = new DrivingOrderRegisterDTO();
//        $drivingOrderDTO->id = $drivingOrder->getId();
//
//        return $drivingOrderDTO;
//    }
//
//    /**
//     * @param $drivingOrders
//     * @return array
//     */
//    public function drivingOrdersToDrivingOrderEmbeddedListDTOs($drivingOrders) {
//        $dtoArray = array();
//        foreach ($drivingOrders as $drivingOrder) {
//            $dtoArray[] = $this->drivingOrdersToDrivingOrderEmbeddedListDTO($drivingOrder);
//        }
//        return $dtoArray;
//    }
//
//    /**
//     * @param DrivingOrder $drivingOrder
//     * @return DrivingOrderEmbeddedListDTO
//     */
//    public function drivingOrdersToDrivingOrderEmbeddedListDTO(DrivingOrder $drivingOrder) {
//        $drivingOrderEmbeddedListDTO = new DrivingOrderEmbeddedListDTO();
//        $drivingOrderEmbeddedListDTO->id = $drivingOrder->getId();
//        $drivingOrderEmbeddedListDTO->personId = $drivingOrder->getPerson()->getId();
//        $drivingOrderEmbeddedListDTO->subject = $drivingOrder->getSubject();
//        $drivingOrderEmbeddedListDTO->startDate = $drivingOrder->getStartDate()->format('d.m.Y');
//        $drivingOrderEmbeddedListDTO->endDate = $drivingOrder->getEndDate()->format('d.m.Y');
//        return $drivingOrderEmbeddedListDTO;
//    }
}