<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:05
 */

namespace Tixi\App\AppBundle\Ride;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyLeastDistance;
use Tixi\App\Ride\RideManagement;
use Tixi\CoreDomain\Dispo\Shift;

/**
 * Class RideManagementImpl
 * @package Tixi\App\AppBundle\Ride
 */
class RideManagementImpl extends ContainerAware implements RideManagement {
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
        //TODO: Feasibility with 1 current strategy (~10sec)or timewindow?

//        $feasibleNode = RideNode::registerFeasibleRide($time, $direction, $duration, $additionalTime);
//
//        $shift = $this->getResponsibleShiftForDayAndTime($day, $time);
//
//        if ($shift === null) {
//            echo "No Shift found for node time\n";
//            return false;
//        }
//
//        $vehicles = $this->getAvailableVehiclesForDay($day);
//        $drivingPools = $shift->getDrivingPools();
//        $drivingMissions = $this->getDrivingMissionsInShift($shift);
//
//        /**
//         * feasibility checks only time windows in a possible configuration
//         */
//        $rideStrategy = new RideStrategyTimeWindow();
//        $rideConfigurator = new RideConfigurator($drivingMissions, $drivingPools, $vehicles, $rideStrategy);
//        $rideConfigurator->addAdditionalRideNode($feasibleNode);
//        $rideConfig = $rideConfigurator->buildConfiguration();
//
//        echo "Shift:" . $this->shiftStart . " - " . $this->shiftEnd . "\n";
//        $rideNodeLists = $rideConfig->getRideNodeLists();
//        foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
//            $rideNodes = $rideNodeList->getRideNodes();
//            echo $drivePoolId . "\t|";
//            /**@var $node RideNode */
//            foreach ($rideNodes as $node) {
//                echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
//            }
//            echo "\n";
//        }
//
//        return !$rideConfig->hasNotFeasibleNodes();

        $dispo = $this->container->get('tixi_app.dispomanagement');

        $shift = $dispo->getResponsibleShiftForDayAndTime($day, $time);
        $day = $shift->getDate();
        $vehicles = $dispo->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools();
        $drivingMissions = $dispo->getDrivingMissionsInShift($shift);

        $rideStrategy = new RideStrategyLeastDistance();
        $rideConfigurator = new ConfigurationBuilder($drivingMissions, $drivingPools, $vehicles, $rideStrategy);
        $emptyRides = $rideConfigurator->buildAllPossibleEmptyRides();

        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        $emptyRides = $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);
        echo "\n\nFilled " . count($emptyRides) . " emptyRideNodes with routing informations in: " . ($e - $s) . "s\n";

        $s = microtime(true);
        //ride configuration with factor of all nodes (change all first entries once) and the same amount for shuffling
        $rideConfiguration = $rideConfigurator->buildConfiguration();
        $e = microtime(true);
        echo "Built rideConfiguration in: " . ($e - $s) . "s\n";

        $rideNodeLists = $rideConfiguration->getRideNodeLists();
        foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
            $rideNodes = $rideNodeList->getRideNodes();
            echo $drivePoolId . "\t|";
            /**@var $node RideNode */
            foreach ($rideNodes as $node) {
                echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
            }
            echo "\n";
        }

        return !$rideConfiguration->hasNotFeasibleNodes();
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a Shift
     * @param \Tixi\CoreDomain\Dispo\Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift) {
        $dispo = $this->container->get('tixi_app.dispomanagement');

        $day = $shift->getDate();
        $vehicles = $dispo->getAvailableVehiclesForDay($day);
        $drivingPools = $shift->getDrivingPools();
        $drivingMissions = $dispo->getDrivingMissionsInShift($shift);

        $rideStrategy = new RideStrategyLeastDistance();
        $rideConfigurator = new ConfigurationBuilder($drivingMissions, $drivingPools, $vehicles, $rideStrategy);

        $emptyRides = $rideConfigurator->buildAllPossibleEmptyRides();

        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        $emptyRides = $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);
        echo "\n\nFilled " . count($emptyRides) . " emptyRideNodes with routing informations in: " . ($e - $s) . "s\n";

        $s = microtime(true);
        //ride configuration with factor of all nodes (change all first entries once) and the same amount for shuffling
        $rideConfigurations = $rideConfigurator->buildConfigurations(count($drivingMissions) * 2);
        $e = microtime(true);
        echo "Built rideConfiguration in: " . ($e - $s) . "s\n";

        foreach ($rideConfigurations as $rideConfig) {
            echo "\nConfiguration total empty rides time:\t" . $rideConfig->getTotalEmptyRideTime() . "min\n";
            echo "Configuration total empty ride distance:\t" . $rideConfig->getTotalEmptyRideDistance() / 1000 . "km\n";
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

}