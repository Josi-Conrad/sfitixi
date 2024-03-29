<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.06.14
 * Time: 00:43
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderOutwardTimeException;
use Tixi\ApiBundle\Form\Shared\DrivingOrderTime;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Interfaces\AddressAssembler;
use Tixi\App\Routing\RouteManagement;
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Zone;

class RepeatedDrivingOrderAssembler {

    /** @var  AddressAssembler $addressAssembler */
    protected $addressAssembler;
    /** @var  RouteManagement $routeManagement */
    protected $routeManagement;
    /** @var  ZonePlanManagement $zonePlanManagement */
    protected $zonePlanManagement;

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @param Passenger $passenger
     * @return RepeatedDrivingOrderPlan
     */
    public function registerDTOtoNewDrivingOrderPlan(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger) {
        /** @var Address $fromAddress */
        $fromAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressFrom);
        /** @var Address $toAddress */
        $toAddress = $this->addressAssembler->addressLookaheadDTOtoAddress($registerDTO->lookaheadaddressTo);
        /** @var Zone $zone */
        $zone = $this->zonePlanManagement->getZoneWithHighestPriorityForCities(array($fromAddress->getCity(), $toAddress->getCity()));
        $route = $this->routeManagement->getRouteFromAddresses($fromAddress, $toAddress);

        $drivingOrderPlan = RepeatedDrivingOrderPlan::registerRepeatedDrivingOrderPlan(
            $registerDTO->anchorDate,
            $registerDTO->withHolidays,
            $registerDTO->compagnion,
            $registerDTO->endDate,
            $registerDTO->memo,
            $registerDTO->additionalTime
        );
        $drivingOrderPlan->assignPassenger($passenger);
        $drivingOrderPlan->assignRoute($route);
        if(null !== $zone) {
            $drivingOrderPlan->assignZone($zone);
        }
        $passenger->assignRepeatedDrivingOrderPlan($drivingOrderPlan);
        return $drivingOrderPlan;
    }

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     */
    public function registerDTOtoDrivingOrderPlan(DrivingOrderRegisterDTO $registerDTO, RepeatedDrivingOrderPlan $drivingOrderPlan) {
        $drivingOrderPlan->update(
            $registerDTO->anchorDate,
            $registerDTO->withHolidays,
            $registerDTO->compagnion,
            $registerDTO->endDate,
            $registerDTO->memo,
            $registerDTO->additionalTime
        );
    }

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @return array|ArrayCollection
     * @throws \Tixi\ApiBundle\Form\Dispo\DrivingOrderOutwardTimeException
     */
    public function registerDTOtoRepeatedDrivingOrders(DrivingOrderRegisterDTO $registerDTO) {
        $repeatedDrivingOrders = new ArrayCollection();
        if(!empty($registerDTO->mondayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(1, $registerDTO->mondayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->mondayOrderTime->returnTime)) {
            if(empty($registerDTO->mondayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(1, $registerDTO->mondayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->tuesdayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(2, $registerDTO->tuesdayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->tuesdayOrderTime->returnTime)) {
            if(empty($registerDTO->tuesdayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(2, $registerDTO->tuesdayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->wednesdayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(3, $registerDTO->wednesdayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->wednesdayOrderTime->returnTime)) {
            if(empty($registerDTO->wednesdayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(3, $registerDTO->wednesdayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->thursdayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(4, $registerDTO->thursdayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->thursdayOrderTime->returnTime)) {
            if(empty($registerDTO->thursdayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(4, $registerDTO->thursdayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->fridayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(5, $registerDTO->fridayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->fridayOrderTime->returnTime)) {
            if(empty($registerDTO->fridayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(5, $registerDTO->fridayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->saturdayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(6, $registerDTO->saturdayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->saturdayOrderTime->returnTime)) {
            if(empty($registerDTO->saturdayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(6, $registerDTO->saturdayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        if(!empty($registerDTO->sundayOrderTime->outwardTime)) {
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(7, $registerDTO->sundayOrderTime->outwardTime, RepeatedDrivingOrder::OUTWARD_DIRECTION);
        }
        if(!empty($registerDTO->sundayOrderTime->returnTime)) {
            if(empty($registerDTO->sundayOrderTime->outwardTime)) {
                throw new DrivingOrderOutwardTimeException();
            }
            $repeatedDrivingOrders[] = RepeatedDrivingOrder::registerRepeatedDrivingOrder(7, $registerDTO->sundayOrderTime->returnTime, RepeatedDrivingOrder::RETURN_DIRECTION);
        }
        return $repeatedDrivingOrders;
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     * @return DrivingOrderRegisterDTO
     */
    public function drivingOrderPlanToRegisterDTO(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan) {
        $registerDTO = new DrivingOrderRegisterDTO();
        $registerDTO->id = $repeatedDrivingOrderPlan->getId();
        $registerDTO->anchorDate = $repeatedDrivingOrderPlan->getAnchorDate();
        $registerDTO->lookaheadaddressFrom = $this->addressAssembler->addressToAddressLookaheadDTO($repeatedDrivingOrderPlan->getRoute()->getStartAddress());
        $registerDTO->lookaheadaddressTo = $this->addressAssembler->addressToAddressLookaheadDTO($repeatedDrivingOrderPlan->getRoute()->getTargetAddress());
        $registerDTO->zoneName = $repeatedDrivingOrderPlan->getZone()->getName();
        $registerDTO->isRepeated = true;
        $registerDTO->withHolidays = $repeatedDrivingOrderPlan->getWithHolidays();
        $registerDTO->compagnion = $repeatedDrivingOrderPlan->getCompanion();
        $registerDTO->memo = $repeatedDrivingOrderPlan->getMemo();
        $registerDTO->additionalTime = $repeatedDrivingOrderPlan->getAdditionalTime();
        $registerDTO->endDate = ($repeatedDrivingOrderPlan->getEndingDate() != DateTimeService::getMaxDateTime()) ? $repeatedDrivingOrderPlan->getEndingDate() : null;
        $repeatedOrders = $repeatedDrivingOrderPlan->getRepeatedDrivingOrdersAsArray();
        /** @var RepeatedDrivingOrder $repeatedOrder*/
        foreach($repeatedOrders as $repeatedOrder) {
            $outwardTime = null;
            $returnTime = null;
            if($repeatedOrder->getWeekday()===1) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->mondayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->mondayOrderTime);
            }
            if($repeatedOrder->getWeekday()===2) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->tuesdayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->tuesdayOrderTime);
            }
            if($repeatedOrder->getWeekday()===3) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->wednesdayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->wednesdayOrderTime);
            }
            if($repeatedOrder->getWeekday()===4) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->thursdayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->thursdayOrderTime);
            }
            if($repeatedOrder->getWeekday()===5) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->fridayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->fridayOrderTime);
            }
            if($repeatedOrder->getWeekday()===6) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->saturdayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->saturdayOrderTime);
            }
            if($repeatedOrder->getWeekday()===7) {
                if($repeatedOrder->getDirection()===RepeatedDrivingOrder::OUTWARD_DIRECTION) {
                    $outwardTime = $repeatedOrder->getPickUpTime();
                }elseif($repeatedOrder->getDirection()===RepeatedDrivingOrder::RETURN_DIRECTION) {
                    $returnTime = $repeatedOrder->getPickUpTime();
                }
                $registerDTO->sundayOrderTime = $this->createOrUpdateOrderTimeDTO($outwardTime, $returnTime, $registerDTO->sundayOrderTime);
            }
        }
        return $registerDTO;

    }

    /**
     * @param null $outwardTime
     * @param null $returnTime
     * @param DrivingOrderTimeDTO $orderTimeDTO
     * @return DrivingOrderTimeDTO
     */
    protected function createOrUpdateOrderTimeDTO($outwardTime = null, $returnTime = null, DrivingOrderTimeDTO $orderTimeDTO = null) {
        $orderTimeDTO = (null !== $orderTimeDTO) ? $orderTimeDTO : new DrivingOrderTimeDTO();
        if(null !== $outwardTime) {
            $orderTimeDTO->outwardTime = $outwardTime;
        }
        if(null !== $returnTime) {
            $orderTimeDTO->returnTime = $returnTime;
        }
        return $orderTimeDTO;
    }

    /**
     * @param $orderPlans
     * @return array
     */
    public function orderPlansToEmbeddedListDTOs($orderPlans) {
        $dtoArray = array();
        foreach ($orderPlans as $orderPlan) {
            $dtoArray[] = $this->orderPlanToEmbeddedListDTOs($orderPlan);
        }
        return $dtoArray;
    }

    /**
     * @param RepeatedDrivingOrderPlan $orderPlan
     * @return RepeatedDrivingOrderEmbeddedListDTO
     */
    public function orderPlanToEmbeddedListDTOs(RepeatedDrivingOrderPlan $orderPlan) {
        $dto = new RepeatedDrivingOrderEmbeddedListDTO();
        $dto->id = $orderPlan->getId();
        $dto->anchorDate = $orderPlan->getAnchorDate()->format('d.m.Y');
        $dto->endDate = ($orderPlan->getEndingDate()!=DateTimeService::getMaxDateTime()) ? $orderPlan->getEndingDate()->format('d.m.Y') : 'repeateddrivingorder.validtillrecalled';
        $dto->memo = $orderPlan->getMemo();
        return $dto;
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
} 