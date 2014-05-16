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
     * @var \Tixi\ApiBundle\Helper\DateTimeService
     */
    protected $time;
    /**
     * @var DrivingOrderRepository
     */
    protected $drivingOrderRepo;
    /**
     * @var ShiftRepository
     */
    protected $shiftRepo;
    /**
     * @var DrivingMissionRepository
     */
    protected $drivingMissionRepo;

    /**
     * checks if a drivingOrder is possible
     * @param \Tixi\CoreDomain\Dispo\DrivingOrder $drivingOrder
     * @return mixed
     */
    public function checkFeasibility(DrivingOrder $drivingOrder) {

        $this->time = $this->container->get('tixi_api.datetimeservice');
        $this->drivingOrderRepo = $this->container->get('drivingorder_repository');
        $this->shiftRepo = $this->container->get('shift_repository');
        $this->drivingMissionRepo = $this->container->get('drivingmission_repository');

        $em = $this->container->get('entity_manager');
        $workingMonthRepo = $this->container->get('workingmonth_repository');
        $workingDayRepo = $this->container->get('workingday_repository');
        $shiftTypeRepo = $this->container->get('shifttype_repository');
        $drivingPoolRepo = $this->container->get('drivingpool_repository');
        $passengerRepo = $this->container->get('passenger_repository');
        $driverRepo = $this->container->get('driver_repository');
        $vehicleRepo = $this->container->get('vehicle_repository');
        $addressRepo = $this->container->get('address_repository');
        $poiRepo = $this->container->get('poi_repository');
        $routeRepo = $this->container->get('route_repository');
        $reDrivingOrderRepo = $this->container->get('repeateddrivingorder_repository.doctrine');
        $reDrivingOrderPlanRepo = $this->container->get('repeateddrivingorderplan_repository.doctrine');
        $routeManagement = $this->container->get('tixi_app.routemanagement');

        $day = $drivingOrder->getPickUpDate();
        $shift = $this->getResponsibleShiftForOrder($drivingOrder);

        if ($shift === null) {
            return false;
        }

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
                $first = $sort[0];
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

            $missionNode = new RideNode(
                RideNode::RIDE_PASSENGER,
                $drivingMission->getServiceMinuteOfDay(),
                $drivingMission->getServiceMinuteOfDay() + $drivingMission->getServiceDuration(),
                $startAddress, $targetAddress
            );
            array_push($missionNodes, $missionNode);
        }

        /** sort Mission by startMinutes */
        usort($missionNodes, function($a, $b){
            return ($a->startMinute > $b->startMinute);
        });

        foreach ($missionNodes as $key => $value) {
            echo "$key: $value->startMinute \n";
        }

        //TODO: get vehicles/pools, compare, create configurationSet


        echo "\n";
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan() {
        // TODO: Implement getOptimizedDayPlan() method.
    }

    private function getResponsibleShiftForOrder(DrivingOrder $order) {
        $shiftsForDay = $this->shiftRepo->findShiftsForDay($order->getPickUpDate());

        $pickTime = $this->time->convertToLocalDateTime($order->getPickUpTime());
        $pickMinutes = $this->time->getMinutesOfDay($pickTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $this->time->convertToLocalDateTime($shift->getStart());
            $endTime = $this->time->convertToLocalDateTime($shift->getEnd());
            $shiftMinutesStart = $this->time->getMinutesOfDay($startTime);
            $shiftMinutesEnd = $this->time->getMinutesOfDay($endTime);
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
        $matchingDrivingMissions = array();

        $startTime = $this->time->convertToLocalDateTime($shift->getStart());
        $endTime = $this->time->convertToLocalDateTime($shift->getEnd());

        $shiftMinutesStart = $this->time->getMinutesOfDay($startTime);
        $shiftMinutesEnd = $this->time->getMinutesOfDay($endTime);

        $drivingMissions = $this->drivingMissionRepo->findDrivingMissionsForDay($shift->getDate());
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
}