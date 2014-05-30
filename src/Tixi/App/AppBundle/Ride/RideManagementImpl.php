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
     * @return mixed
     */
    public function checkFeasibility(\DateTime $dayTime, $direction, $duration, $additionalTime) {
        $s = microtime(true);

        $dispoManagement = $this->container->get('tixi_app.dispomanagement');

        $shift = $dispoManagement->getResponsibleShiftForDayAndTime($dayTime);
        //if there is not already a planed shift in future, it should be feasible
        //TODO: Check with repeatedDrivingMissions, but we cannot check too far in future...
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

        $rideConfigurator->buildConfigurationFromExistingMissions();

        //ride configuration with factor of all nodes (change all first entries once) and the same amount for shuffling
        $rideConfiguration = $rideConfigurator->buildConfiguration();

        //already not feasible nodes in time
        if ($rideConfiguration->hasNotFeasibleNodes()) {
            return false;
        }

        //analyze configuration with an feasibleRide object if its fit
        $rideAnalyzer = new ConfigurationAnalyzer($rideConfiguration);
        $isFeasible = $rideAnalyzer->checkIfNodeIsFeasibleInConfiguration($feasibleNode);

        $e = microtime(true);
//        echo "Check feasibility TimeWindow in: " . ($e - $s) . "s\n";
//        $this->printConfiguration($rideConfiguration);

        return $isFeasible;
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a Shift
     * @param \Tixi\CoreDomain\Dispo\Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift) {
        //STRATEGY for RideOptimization
        $rideStrategy = new RideStrategyAnnealing();

        //TODO: Problem with shift before and after + pool assignments
        $em = $this->container->get('entity_manager');
        $em->beginTransaction();

        $dispoManagement = $this->container->get('tixi_app.dispomanagement');

        $day = $shift->getDate();
        $vehicles = $dispoManagement->getAvailableVehiclesForDay($day);

        $drivingPools = $shift->getDrivingPoolsAsArray();
        if (count($drivingPools) < 1) {
            $em->rollback();
            $this->fallback();
        }

        $drivingMissions = $dispoManagement->getDrivingMissionsInShift($shift);
        if (count($drivingMissions) < 1) {
            $em->rollback();
            $this->fallback();
        }

        //clean drivingPools for new optimization
        foreach ($drivingPools as $pool) {
            $pool->removeDrivingMissions();
        }

        $rideConfigurator = new ConfigurationBuilder($drivingMissions, $drivingPools, $vehicles, $rideStrategy);

        $emptyRides = $rideConfigurator->buildAllPossibleEmptyRides();

        $s = microtime(true);
        $routeManagement = $this->container->get('tixi_app.routemanagement');
        //get routing information from routing machine and fill node objects
        $emptyRides = $routeManagement->fillRoutesForMultipleRideNodes($emptyRides);
        $e = microtime(true);
        echo "\n\nFilled " . count($emptyRides) . " rides from RoutingMachine in: " . ($e - $s) . "s\n";

        $s = microtime(true);
        //ride configuration with factor of all nodes (change all first entries once) and the same amount for shuffling
        $rideConfigurations = $rideConfigurator->buildConfigurations(count($drivingMissions) * 2);
        $e = microtime(true);
        echo "Built rideConfiguration in: " . ($e - $s) . "s\n";

        //sort by certain tactic
        //TODO: configuration with 13 vehicles and 70km emptyRide, and one with 14 vehicles and 60km emptyRide, which one is better?
        $rideConfigurator->sortRideConfigurationsByUsedVehicleAndDistance($rideConfigurations);

        if (count($rideConfigurations) < 1) {
            $em->rollback();
            $this->fallback();
        }

        //success on setting vehicles to a configuration
        $success = false;
        $rideAnalyzer = null;
        $rideConfiguration = null;
        //get best feasible configuration (first element in sorted list)
        while (!$success) {
            if (count($rideConfigurations) < 1) {
                $em->rollback();
                $this->fallback();
            }
            $rideConfiguration = array_shift($rideConfigurations);
            $rideAnalyzer = new ConfigurationAnalyzer($rideConfiguration);
            $rideAnalyzer->assignPoolsToRideNodeList();
            $success = $rideAnalyzer->assignVehiclesToBestConfiguration($vehicles);
        }
        if (!$success) {
            $em->rollback();
            $this->fallback();
        }

        $rideAnalyzer->assignMissionsToPools($rideConfiguration);

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
        echo "Choosen Vehicles: ";
        foreach ($rideConfig->getDrivingPools() as $pool) {
            if ($pool->hasAssociatedVehicle()) {
                echo $pool->getVehicle()->getName() . ", ";
            }
        }
        echo "\nNot Feasible Nodes:\t" . count($rideConfig->getNotFeasibleNodes()) . "\n";
        $rideNodeLists = $rideConfig->getRideNodeLists();
        foreach ($rideNodeLists as $drivePoolId => $rideNodeList) {
            $rideNodes = $rideNodeList->getRideNodes();
            echo $drivePoolId . " w:" . $rideNodeList->getMaxWheelChairsOnRide() . " p:" . $rideNodeList->getMaxPassengersOnRide() . "\t|";
            /**@var $node RideNode */
            foreach ($rideNodes as $node) {
                echo "(" . $node->startMinute . "-" . $node->endMinute . ")\t";
            }
            echo "\n";
        }
        echo "\n";
    }

    private function fallback() {
        echo "NO FEASIBLE CONFIG";
        return false;
    }
}