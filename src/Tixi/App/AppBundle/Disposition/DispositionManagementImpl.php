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
     * Shift informations of start/end minutes
     * @var
     */
    protected $shiftStart;
    protected $shiftEnd;

    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @param $direction
     * @param $duration
     * @param $additionalTime
     * @return mixed
     */
    public function checkFeasibility(\DateTime $day, \DateTime $time, $direction, $duration, $additionalTime) {
        //TODO: create controller and check feasibility only with pickupTime, duration and passengerID + additionalTime
        $feasibleNode = RideNode::registerFeasibleRide($time, $direction, $duration, $additionalTime);

        $shift = $this->getResponsibleShiftForDayAndTime($day, $time);

        if ($shift === null) {
            echo "No Shift found for node time\n";
            return false;
        }

        $vehicles = $this->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools()->toArray();
        $drivingMissions = $this->getDrivingMissionsInShift($shift);

        echo "\nMissions for Shift: " . count($drivingMissions) . "\n   ";

        /**
         * feasibility checks only time windows in a possible configuration
         */
        $rideConfig = new RideConfiguration($drivingMissions, $drivingPools, $vehicles, RideConfiguration::ONLY_TIME_WINDOWS);
        $rideConfig->addAdditionalRideNode($feasibleNode);
        $rideConfig->buildConfiguration();

        $rideConfigurationArray = $rideConfig->getRideConfigurationArray();
        echo "Shift:" . $this->shiftStart . " - " . $this->shiftEnd . "\n";
        foreach ($rideConfigurationArray as $poolId => $poolNodes) {
            echo $poolId . "\t|";
            /**@var $node RideNode */
            foreach ($poolNodes as $node) {
                echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
            }
            echo "\n";
        }

        return !$rideConfig->gotNotFeasibleNodes();
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

        //TODO: get vehicles/pools, compare, create configurationSet
        //array with RaidConfiguration[]
        //RaidConfiguration got array[][] with vehicles and orders per shift

        /**
         * feasibility checks only time windows in a possible configuration
         */
        $rideConfig = new RideConfiguration($drivingMissions, $drivingPools, $vehicles, RideConfiguration::LEAST_KILOMETER);
//        $rideConfig->buildConfiguration();

        $emptyRides = $rideConfig->getAllPossibleEmptyRides();
        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);

        echo "Filled " . count($emptyRides) . " emptyRideNodes in: " . ($e - $s) . "s\n";
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan() {

    }

    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @return null|Shift
     */
    private function getResponsibleShiftForDayAndTime(\DateTime $day, \DateTime $time) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $shiftRepo = $this->container->get('shift_repository');
        $shiftsForDay = $shiftRepo->findShiftsForDay($day);

        $pickTime = $timeService->convertToLocalDateTime($time);
        $pickMinutes = $timeService->getMinutesOfDay($pickTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
            $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());
            echo "Shift: " . $startTime->format('H:i') . " - " . $endTime->format('H:i');
            $shiftMinutesStart = $timeService->getMinutesOfDay($startTime);
            $shiftMinutesEnd = $timeService->getMinutesOfDay($endTime);
            if ($pickMinutes >= $shiftMinutesStart && $pickMinutes <= $shiftMinutesEnd) {
                $this->shiftStart = $shiftMinutesStart;
                $this->shiftEnd = $shiftMinutesEnd;
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
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $drivingMissionRepo = $this->container->get('drivingmission_repository');
        $matchingDrivingMissions = array();

        $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
        $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());

        $shiftMinutesStart = $timeService->getMinutesOfDay($startTime);
        $shiftMinutesEnd = $timeService->getMinutesOfDay($endTime);

        $drivingMissions = $drivingMissionRepo->findDrivingMissionsForDay($shift->getDate());
        foreach ($drivingMissions as $drivingMission) {
            /**@var $drivingMission \Tixi\CoreDomain\Dispo\DrivingMission */
            $startMinute = $drivingMission->getServiceMinuteOfDay();
            $endMinute = $startMinute + $drivingMission->getServiceDuration();

            //TODO: Tactic if all missions with beginnTime in Shift or also with a part of endTime?
            /** start or end of the order laps into a shift time */
            if ($startMinute >= $shiftMinutesStart && $endMinute <= $shiftMinutesEnd ||
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
        $timeService = $this->container->get('tixi_api.datetimeservice');
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
                    $spStart = $timeService->convertToLocalDateTime($servicePlan->getStart())->setTime(0, 0);
                    $spEnd = $timeService->convertToLocalDateTime($servicePlan->getEnd())->setTime(0, 0);
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