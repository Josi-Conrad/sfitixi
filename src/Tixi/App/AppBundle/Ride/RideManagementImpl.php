<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:05
 */

namespace Tixi\App\AppBundle\Ride;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Helper\WeekdayService;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyGenericLeastDistance;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyAnnealing;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyTimeWindow;
use Tixi\App\Ride\RideManagement;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\Shift;

/**
 * Class RideManagementImpl
 * @package Tixi\App\AppBundle\Ride
 */
class RideManagementImpl extends ContainerAware implements RideManagement {
    /**
     * @param \DateTime $dayTime
     * @param $direction
     * @param $duration
     * @param $additionalTime
     * @return bool
     */
    public function checkFeasibility(\DateTime $dayTime, $direction, $duration, $additionalTime = 0) {
        $dispoManagement = $this->container->get('tixi_app.dispomanagement');

        $shift = $dispoManagement->getResponsibleShiftForDayAndTime($dayTime);

        //if there is not already a planed shift in future, it should be feasible
        if ($shift === null) {
            return true;
        }

        $day = $shift->getDate();
        $vehicles = $dispoManagement->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPoolsAsArray();
        $drivingMissions = $dispoManagement->getDrivingMissionsInShift($shift);

        $feasibleNode = RideNode::registerFeasibleRide($dayTime, $direction, $duration, $additionalTime);

        $rideStrategy = new RideStrategyTimeWindow();
        $rideConfigurator = new ConfigurationBuilder($drivingMissions, $drivingPools, $vehicles, $rideStrategy);

        $rideConfigurator->createConfigurationFromExistingMissions();

        //ride configuration with factor of all nodes (change all first entries once) and the same amount for shuffling
        $rideConfiguration = $rideConfigurator->buildConfiguration();

        //already not feasible nodes in time
        if ($rideConfiguration->hasNotFeasibleNodes()) {
            return false;
        }

        //analyze configuration with an feasibleRide object if its fit
        $rideAnalyzer = new ConfigurationAnalyzer($rideConfiguration);
        $isFeasible = $rideAnalyzer->checkIfNodeIsFeasibleInConfiguration($feasibleNode);

        return $isFeasible;
    }


    /**
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDate
     * @param $weekday
     * @param $direction
     * @param $duration
     * @param int $additionalTime
     * @throws \LogicException
     * @return bool
     */
    public function checkRepeatedFeasibility(\DateTime $fromDateTime, \DateTime $toDate, $weekday, $direction, $duration, $additionalTime = 0) {
        if ($weekday < 1 || $weekday > 7) {
            throw new \LogicException('weekday not in ISO-8601 range');
        }
        $repeatedDrivingAssertionPlanRepo = $this->container->get('repeateddrivingassertionplan_repository');
        $timeService = $this->container->get('tixi_api.datetimeservice');

        $utcDateTime = $timeService->convertToUTCDateTime($fromDateTime);
        $weekDate = clone $utcDateTime;
        $weekDate->modify('next ' . WeekdayService::$numericToWeekdayConverter[$weekday]);
        $weekDate->setTime($utcDateTime->format('H'), $utcDateTime->format('i'));

        $plans = $repeatedDrivingAssertionPlanRepo->findAllActivePlansAtTheMoment();

        $vehicleRepo = $this->container->get('vehicle_repository');
        $allVehicles = $vehicleRepo->findAllNotDeleted();

        $countOfVehicles = count($allVehicles);
        $countOfExistingAssertions = 0;
        //pattern like matching on all available RepeatedDrivingAssertions,
        //if more then available vehicle matches, it is not feasible
        foreach ($plans as $plan) {
            foreach ($plan->getRepeatedDrivingAssertionsAsArray() as $assertion) {
                if ($assertion->matchingDateTime($weekDate)) {
                    $countOfExistingAssertions++;
                }
            }
        }
        if ($countOfExistingAssertions > $countOfVehicles) {
            //not feasible
            return false;
        }
        //feasible
        return true;
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a Shift
     * @param \Tixi\CoreDomain\Dispo\Shift $shift
     * @return bool
     */
    public function buildOptimizedPlanForShift(Shift $shift) {
        //TODO: checking drivers and missions with shift before and after current shift + pool assignments
        $em = $this->container->get('entity_manager');
        $em->beginTransaction();

        //STRATEGY for RideOptimization
        $rideStrategy = new RideStrategyAnnealing();

        $dispoManagement = $this->container->get('tixi_app.dispomanagement');

        $day = $shift->getDate();
        $vehicles = $dispoManagement->getAvailableVehiclesForDay($day);

        $drivingPools = $shift->getDrivingPoolsAsArray();
        if (count($drivingPools) < 1) {
            $em->rollback();
            return $this->fallback();
        }

        $drivingMissions = $dispoManagement->getDrivingMissionsInShift($shift);
        if (count($drivingMissions) < 1) {
            $em->rollback();
            return $this->fallback();
        }

        //clean drivingPools for new optimization
        foreach ($drivingPools as $pool) {
            $pool->removeDrivingMissions();
            $pool->removeVehicle();
        }

        $configurationBuilder = new ConfigurationBuilder($drivingMissions, $drivingPools, $vehicles, $rideStrategy);

        //get all empty Rides
        $emptyRides = $configurationBuilder->buildAllPossibleEmptyRides();

        //get routing information from routing machine and fill node objects
        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        $emptyRides = $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);
        echo "\n\nFilled " . count($emptyRides) . " rides from RoutingMachine in: " . ($e - $s) . "s\n";

        //create ride configurations with strategy
        $s = microtime(true);
        $rideConfigurations = $configurationBuilder->buildConfigurations();
        $e = microtime(true);
        echo "Built rideConfiguration in: " . ($e - $s) . "s\n";
        if ($rideConfigurations === null) {
            $em->rollback();
            return $this->fallback();
        }

        //sort configurations by least distance
        $configurationBuilder->sortRideConfigurationsByDistance($rideConfigurations);

        //success on setting vehicles to a configuration
        $success = false;
        $configurationAnalyzer = null;
        $rideConfiguration = null;

        //get best feasible configuration (first element in sorted list)
        while (!$success) {
            if (count($rideConfigurations) < 1) {
                $em->rollback();
                return $this->fallback();
            }
            $rideConfiguration = array_shift($rideConfigurations);
            $configurationAnalyzer = new ConfigurationAnalyzer($rideConfiguration);

            if ($configurationAnalyzer->assignVehiclesToConfiguration($vehicles)) {
                $success = $configurationAnalyzer->assignPoolsToRideNodeList();
            }
        }
        if (!$success) {
            $em->rollback();
            return $this->fallback();
        }

        //if all configuration and arrangements are possible, set finally missions and vehicle to pool
        $configurationAnalyzer->assignMissionsAndVehiclesToPool($rideConfiguration);

        $this->printConfiguration($rideConfiguration);

        //if everything worked return successfully
        $em->commit();
        $em->flush();
        return true;
    }

    /**
     * for debug informations
     * @param RideConfiguration $rideConfig
     */
    private function printConfiguration(RideConfiguration $rideConfig) {
        echo "Configuration Vehicles: " . $rideConfig->getAmountOfUsedVehicles() . " - ";
        echo "Distance: " . $rideConfig->getTotalDistance() / 1000 . "km - ";
        echo "EmptyRideTime: " . $rideConfig->getTotalEmptyRideTime() . "min - ";
        echo "EmptyRideDistance: " . $rideConfig->getTotalEmptyRideDistance() / 1000 . "km\n";
        echo "Not Feasible Nodes:\t" . count($rideConfig->getNotFeasibleNodes()) . "\n";
        $rideNodeLists = $rideConfig->getRideNodeLists();
        foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
            $rideNodes = $rideNodeList->getRideNodes();
            echo $drivePoolId . " W:" . $rideNodeList->getMaxWheelChairsOnRide() . " P:" . $rideNodeList->getMaxPassengersOnRide() . "\t";
            if ($rideNodeList->getDrivingPool()->hasAssociatedDriver()) {
                echo $rideNodeList->getDrivingPool()->getDriver()->getNameString() . "\t";
            }
            if ($rideNodeList->getDrivingPool()->hasAssociatedVehicle()) {
                echo $rideNodeList->getDrivingPool()->getVehicle()->getName() . "\t|";
            }
            /**@var $node RideNode */
            foreach ($rideNodes as $node) {
                echo "(" . $node->drivingMission->getId() . ":" . $node->startAddress->getAddressNameShort() . "->" . $node->targetAddress->getAddressNameShort() . ")\t";
            }
            echo "\n";
        }
        echo "\n";
    }

    /**
     * fallback with action (mail send to inform user?)
     * @return bool
     */
    private function fallback() {
        echo "NO FEASIBLE CONFIG";
        return false;
    }
}