<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:06
 */

namespace Tixi\App\AppBundle\Disposition;


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;

class DispositionManagementImpl extends ContainerAware implements DispositionManagement {
    /**
     * checks if a drivingOrder is possible
     * @param \Tixi\CoreDomain\Dispo\DrivingOrder $drivingOrder
     * @return mixed
     */
    public function checkFeasibility(DrivingOrder $drivingOrder) {
        //TODO: Change to controller and check feasibility only with pickupTime, duration and passengerID
        $day = $drivingOrder->getPickUpDate();
        $shift = $this->getResponsibleShiftForOrder($drivingOrder);
        if ($shift === null) {
            return false;
        }
        $vehicles = $this->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools()->toArray();

        $missionNodes = array();
        $drivingMissions = $this->getDrivingMissionsInShift($shift);
        foreach ($drivingMissions as $drivingMission) {
            /**
             * if DrivingMission got no elements in ServiceOrder => singleOrder
             * if it got elements in it => multiOrder
             */
            if (empty($drivingMission->getServiceOrder())) {
                /**@var $order DrivingOrder */
                $order = $drivingMission->getDrivingOrders()->first();
                $startAddress = $order->getRoute()->getStartAddress();
                $targetAddress = $order->getRoute()->getTargetAddress();
            } else {
                $sort = $drivingMission->getServiceOrder();
                $first = reset($sort);
                $last = count($sort);

                /**@var $sOrder DrivingOrder */
                $fOrder = $drivingMission->getDrivingOrders()->get($sort[$first]);
                /**@var $tOrder DrivingOrder */
                $lOrder = $drivingMission->getDrivingOrders()->get($sort[$last]);

                if ($drivingMission->getDirection() === DrivingMission::SAME_START) {
                    $startAddress = $fOrder->getRoute()->getStartAddress();
                    $targetAddress = $lOrder->getRoute()->getTargetAddress();
                } else {
                    $startAddress = $fOrder->getRoute()->getStartAddress();
                    $targetAddress = $fOrder->getRoute()->getTargetAddress();
                }
            }

            $missionNode = RideNode::registerPassengerRide($drivingMission, $startAddress, $targetAddress);
            array_push($missionNodes, $missionNode);
        }

        //TODO: get vehicles/pools, compare, create configurationSet
        //array with RaidConfiguration[]
        //RaidConfiguration got array[][] with vehicles and orders per shift

//        $tempMission = DrivingMission::registerDrivingMissionFromOrder($drivingOrder);
//        $checkingNode = RideNode::registerPassengerRide(
//            $tempMission, $drivingOrder->getRoute()->getStartAddress(), $drivingOrder->getRoute()->getTargetAddress());

        /**
         * feasibility checks only time windows in a possible configuration
         */
        $rideConfig = new RideConfiguration($missionNodes, $drivingPools, RideConfiguration::ONLY_TIME_WINDOWS);
        $rideConfig->buildConfiguration();
        $emptyRide = $rideConfig->getAllPossibleEmptyRides();

        echo "\n";
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a Shift
     * @param \Tixi\CoreDomain\Dispo\Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift) {
        $day = $shift->getDate();

        $vehicles = $this->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools()->toArray();
        $drivingMissions = $this->getDrivingMissionsInShift($shift);

        $missionNodes = array();
        foreach ($drivingMissions as $drivingMission) {
            /**
             * if DrivingMission got no elements in ServiceOrder => singleOrder
             * if it got elements in it => multiOrder
             */
            if (empty($drivingMission->getServiceOrder())) {
                /**@var $order DrivingOrder */
                $order = $drivingMission->getDrivingOrders()->first();
                $startAddress = $order->getRoute()->getStartAddress();
                $targetAddress = $order->getRoute()->getTargetAddress();
            } else {
                $sort = $drivingMission->getServiceOrder();
                $first = reset($sort);
                $last = count($sort);

                /**@var $sOrder DrivingOrder */
                $fOrder = $drivingMission->getDrivingOrders()->get($sort[$first]);
                /**@var $tOrder DrivingOrder */
                $lOrder = $drivingMission->getDrivingOrders()->get($sort[$last]);

                if ($drivingMission->getDirection() === DrivingMission::SAME_START) {
                    $startAddress = $fOrder->getRoute()->getStartAddress();
                    $targetAddress = $lOrder->getRoute()->getTargetAddress();
                } else {
                    $startAddress = $fOrder->getRoute()->getStartAddress();
                    $targetAddress = $fOrder->getRoute()->getTargetAddress();
                }
            }

            $missionNode = RideNode::registerPassengerRide($drivingMission, $startAddress, $targetAddress);
            array_push($missionNodes, $missionNode);
        }


        foreach ($missionNodes as $key => $value) {
            echo "$key: $value->startMinute \n";
        }

        //TODO: get vehicles/pools, compare, create configurationSet
        //array with RaidConfiguration[]
        //RaidConfiguration got array[][] with vehicles and orders per shift

        /**
         * feasibility checks only time windows in a possible configuration
         */
        $rideConfig = new RideConfiguration($missionNodes, $drivingPools, RideConfiguration::ONLY_TIME_WINDOWS);
        $rideConfig->buildConfiguration();
        $emptyRide = $rideConfig->getAllPossibleEmptyRides();

        echo "\n";
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan(){

    }

    /**
     * @param DrivingOrder $order
     * @return null|Shift
     */
    private function getResponsibleShiftForOrder(DrivingOrder $order) {
        $time = $this->container->get('tixi_api.datetimeservice');
        $shiftRepo = $this->container->get('shift_repository');
        $shiftsForDay = $shiftRepo->findShiftsForDay($order->getPickUpDate());

        $pickTime = $time->convertToLocalDateTime($order->getPickUpTime());
        $pickMinutes = $time->getMinutesOfDay($pickTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $time->convertToLocalDateTime($shift->getStart());
            $endTime = $time->convertToLocalDateTime($shift->getEnd());
            $shiftMinutesStart = $time->getMinutesOfDay($startTime);
            $shiftMinutesEnd = $time->getMinutesOfDay($endTime);
            if ($pickMinutes >= $shiftMinutesStart && $pickMinutes <= $shiftMinutesEnd) {
                return $shift;
            }
        }
        return null;
    }

    /**
     * @param Shift $shift
     * @return DrivingMission[]
     */
    private function getDrivingMissionsInShift(Shift $shift) {
        $time = $this->container->get('tixi_api.datetimeservice');
        $drivingMissionRepo = $this->container->get('drivingmission_repository');
        $matchingDrivingMissions = array();

        $startTime = $time->convertToLocalDateTime($shift->getStart());
        $endTime = $time->convertToLocalDateTime($shift->getEnd());

        $shiftMinutesStart = $time->getMinutesOfDay($startTime);
        $shiftMinutesEnd = $time->getMinutesOfDay($endTime);

        $drivingMissions = $drivingMissionRepo->findDrivingMissionsForDay($shift->getDate());
        foreach ($drivingMissions as $drivingMission) {
            /**@var $drivingMission \Tixi\CoreDomain\Dispo\DrivingMission */
            $startMinute = $drivingMission->getServiceMinuteOfDay();
            $endMinute = $startMinute + $drivingMission->getServiceDuration();

            /** start or end of the order laps into a shift time */
            if ($endMinute >= $shiftMinutesStart && $endMinute <= $shiftMinutesEnd ||
                $startMinute >= $shiftMinutesStart && $startMinute <= $shiftMinutesEnd
            ) {
                array_push($matchingDrivingMissions, $drivingMission);
            }
        }
        return $matchingDrivingMissions;
    }

    /**
     * @param \DateTime $day
     * @return array
     */
    private function getAvailableVehiclesForDay(\DateTime $day) {
        $time = $this->container->get('tixi_api.datetimeservice');
        $vehicleRepo = $this->container->get('vehicle_repository');
        $allVehicles = $vehicleRepo->findAllNotDeleted();
        $vehicles = array();
        foreach ($allVehicles as $vehicle) {
            $servicePlans = $vehicle->getActualServicePlans();
            if ($servicePlans === null) {
                array_push($vehicles, $vehicle);
            } else {
                $isInService = false;
                foreach ($servicePlans as $servicePlan) {
                    $spStart = $time->convertToLocalDateTime($servicePlan->getStart())->setTime(0, 0);
                    $spEnd = $time->convertToLocalDateTime($servicePlan->getEnd())->setTime(0, 0);
                    if (($spStart == $day || $spEnd == $day)
                    ) {
                        $isInService = true;
                    }
                }
                if (!$isInService) {
                    array_push($vehicles, $vehicle);
                }
            }
        }
        return $vehicles;
    }
}