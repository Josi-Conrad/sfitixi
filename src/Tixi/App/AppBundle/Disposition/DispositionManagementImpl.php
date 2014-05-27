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
use Tixi\App\AppBundle\Disposition\RideStrategies\RideStrategyLeastDistance;
use Tixi\App\AppBundle\Disposition\RideStrategies\RideStrategyTimeWindow;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomain\Dispo\ShiftTypeRepository;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

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
        $drivingPools = $shift->getDrivingPools();
        $drivingMissions = $this->getDrivingMissionsInShift($shift);

        /**
         * feasibility checks only time windows in a possible configuration
         */
        $rideStrategy = new RideStrategyTimeWindow();
        $rideConfigurator = new RideConfigurator($drivingMissions, $drivingPools, $vehicles, $rideStrategy);
        $rideConfigurator->addAdditionalRideNode($feasibleNode);
        $rideConfig = $rideConfigurator->buildConfiguration();

        echo "Shift:" . $this->shiftStart . " - " . $this->shiftEnd . "\n";
            $rideNodeLists = $rideConfig->getRideNodeLists();
            foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
                $rideNodes = $rideNodeList->getRideNodes();
                echo $drivePoolId . "\t|";
                /**@var $node RideNode */
                foreach ($rideNodes as $node) {
                    echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
                }
                echo "\n";
            }

        return !$rideConfig->hasNotFeasibleNodes();
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a Shift
     * @param \Tixi\CoreDomain\Dispo\Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift) {
        $day = $shift->getDate();
        $vehicles = $this->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools();
        $drivingMissions = $this->getDrivingMissionsInShift($shift);

        $rideStrategy = new RideStrategyLeastDistance();
        $rideConfigurator = new RideConfigurator($drivingMissions, $drivingPools, $vehicles, $rideStrategy);

        $emptyRides = $rideConfigurator->buildAllPossibleEmptyRides();

        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        $emptyRides = $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);
        echo "\n\nFilled " . count($emptyRides) . " emptyRideNodes with routing informations in: " . ($e - $s) . "s\n";

        $s = microtime(true);
        $rideConfigurations = $rideConfigurator->buildConfigurations(200);
        $e = microtime(true);
        echo "Built rideConfiguration in: " . ($e - $s) . "s\n";

        echo "Shift:" . $this->shiftStart . " - " . $this->shiftEnd . "\n";
        foreach ($rideConfigurations as $rideConfig) {
            echo "Configuration total empty rides time:\t" . $rideConfig->getTotalEmptyRideTime() . "min\n";
            echo "Configuration total distance:\t" . $rideConfig->getTotalDistance() / 1000 . "km\n";
            echo "Not Feasible Nodes:\t" . count($rideConfig->getNotFeasibleNodes()) . "\n";
            $rideNodeLists = $rideConfig->getRideNodeLists();
            foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
                $rideNodes = $rideNodeList->getRideNodes();
                echo $drivePoolId . "\t|";
                /**@var $node RideNode */
                foreach ($rideNodes as $node) {
                    echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
                }
                echo "\n";
            }
        }
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
    public function getResponsibleShiftForDayAndTime(\DateTime $day, \DateTime $time) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $shiftRepo = $this->container->get('shift_repository');
        $shiftsForDay = $shiftRepo->findShiftsForDay($day);

        $pickTime = $timeService->convertToLocalDateTime($time);
        $pickMinutes = $timeService->getMinutesOfDay($pickTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
            $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());
            echo "Shift: " . $startTime->format('H:i') . " - " . $endTime->format('H:i') . "\t";
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
    public function getDrivingMissionsInShift(Shift $shift) {
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
    public function getAvailableVehiclesForDay(\DateTime $day) {
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

    public function processChangeInAmountOfDriversPerShift(Shift $shift, $oldAmount, $newAmount)
    {
        // TODO: Implement processChangeInAmountOfDriversPerShift() method.
        $shift->setAmountOfDrivers($newAmount);
    }

    public function openWorkingMonth($year, $month)
    {
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->container->get('workingmonth_repository');
        /** @var WorkingDayRepository $workingDayRepository */
        $workingDayRepository = $this->container->get('workingday_repository');
        /** @var ShiftRepository $shiftRepository */
        $shiftRepository = $this->container->get('shift_repository');
        /** @var ShiftTypeRepository $shiftTypeRepository */
        $shiftTypeRepository = $this->container->get('shifttype_repository');

        try {
            $date = new \DateTime();
            $date->setDate($year,$month,1);
        }catch (\Exception $e) {
            return null;
        }
        $workingMonth = WorkingMonth::registerWorkingMonth($date);
        $workingMonth->createWorkingDaysForThisMonth();

        $shiftTypes = $shiftTypeRepository->findAllActive();

        $workingDays = $workingMonth->getWorkingDays();
        foreach($workingDays as $workingDay) {
            $workingDayRepository->store($workingDay);
            foreach($shiftTypes as $shiftType) {
                $shift = Shift::registerShift($workingDay, $shiftType);
                $workingDay->assignShift($shift);
                $shiftRepository->store($shift);
            }

        }
        $workingMonthRepository->store($workingMonth);
        return $workingMonth;
    }
}