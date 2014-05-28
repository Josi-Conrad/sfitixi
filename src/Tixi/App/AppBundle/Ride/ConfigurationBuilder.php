<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 17.05.14
 * Time: 10:43
 */

namespace Tixi\App\AppBundle\Ride;


use Tixi\App\AppBundle\Ride\RideConfiguration;
use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategy;
use Tixi\App\AppBundle\Disposition\RideStrategies;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Vehicle;

/**
 * Class ConfigurationBuilder
 * @package Tixi\App\AppBundle\Ride
 */
class ConfigurationBuilder {
    /**
     * @var \Tixi\App\AppBundle\Ride\RideStrategies\RideStrategy
     */
    protected $strategy;
    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;
    /**
     * @var RideNode[]
     */
    protected $rideNodes;
    /**
     * @var RideNode[]
     */
    protected $emptyRideNodes;
    /**
     * @var Vehicle[]
     */
    protected $availableVehicles;
    /**
     * @var Address[]
     */
    protected $vehicleDepotAddresses;
    /**
     * @var RideConfiguration
     */
    protected $rideConfiguration;
    /**
     * @var RideConfiguration[]
     */
    protected $rideConfigurations;

    /**
     * @param DrivingMission[] $drivingMissions
     * @param DrivingPool[] $drivingPools
     * @param Vehicle[] $vehicles
     * @param \Tixi\App\AppBundle\Ride\RideStrategies\RideStrategy $rideStrategy
     */
    public function __construct($drivingMissions, $drivingPools, $vehicles, RideStrategy $rideStrategy) {
        $this->rideNodes = $this->createRideNodesFromDrivingMissions($drivingMissions);
        $this->drivingPools = $drivingPools;
        $this->fillVehicleDepotNodes($vehicles);
        $this->strategy = $rideStrategy;
        $this->emptyRideNodes = array();
    }

    /**
     * build configurations according to tactic
     * @return RideConfiguration
     */
    public function buildConfiguration() {
        $rideConfiguration = $this->strategy->buildConfiguration($this->rideNodes, $this->drivingPools, $this->emptyRideNodes);
        $this->rideConfiguration = $rideConfiguration;
        return $rideConfiguration;
    }

    /**
     * @param $factor
     * @return RideConfiguration[]
     */
    public function buildConfigurations($factor) {
        $rideConfigurations = $this->strategy->buildConfigurations($this->rideNodes, $this->drivingPools, $this->emptyRideNodes, $factor);
        $this->rideConfigurations = $rideConfigurations;
        return $rideConfigurations;
    }

    /**
     * gets hash array with nodes, where key is concatenated coordinate hash (md2) from start and destination
     * @return array
     */
    public function buildAllPossibleEmptyRides() {
        $this->sortNodesByStartMinute($this->rideNodes);

        $workNodes = $this->rideNodes;
        foreach ($workNodes as $key => $workNode) {
            //fill rides from and to vehicleDepot
            foreach ($this->vehicleDepotAddresses as $depotAddress) {
                $depotToNode = RideNode::registerEmptyRide($depotAddress, $workNode->startAddress);
                $nodeToDepot = RideNode::registerEmptyRide($workNode->targetAddress, $depotAddress);
                $this->emptyRideNodes[$depotToNode->getRideHash()] = $depotToNode;
                $this->emptyRideNodes[$nodeToDepot->getRideHash()] = $nodeToDepot;
            }

            //fill possible rides between any time-feasible nodes
            $comparesNodes = $workNodes;
            foreach ($comparesNodes as $compareNode) {
                if ($workNode->endMinute < $compareNode->startMinute) {
                    $node = RideNode::registerEmptyRide($workNode->targetAddress, $compareNode->startAddress);
                    $this->emptyRideNodes[$node->getRideHash()] = $node;
                }
            }
            unset($workNodes[$key]);
        }
        return $this->emptyRideNodes;
    }

    /**
     * creates an array with RideNodes according to a drivingMission with missionId as arrayKey
     * @param DrivingMission[] $drivingMissions
     * @return RideNode[]
     */
    private function createRideNodesFromDrivingMissions($drivingMissions) {
        $missionNodes = array();
        foreach ($drivingMissions as $drivingMission) {
            /**
             * if DrivingMission got no elements in ServiceOrder => singleOrder
             * if elements exist => multiOrder
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
            $missionNodes[$drivingMission->getId()] = $missionNode;
        }
        return $missionNodes;
    }


    /**
     * @param RideNode $feasibleNode
     */
    public function addAdditionalRideNode(RideNode $feasibleNode) {
        $this->rideNodes[] = $feasibleNode;
    }

    /**
     * @return array
     */
    public function getEmptyRides() {
        return $this->emptyRideNodes;
    }

    /**
     * @return RideConfiguration[]
     */
    public function getRideConfigurations() {
        return $this->rideConfigurations;
    }

    /**
     * sort Mission by startMinutes
     * @param RideNode[] $nodes
     */
    public static function sortNodesByStartMinute(&$nodes) {
        usort($nodes, function ($a, $b) {
            return ($a->startMinute > $b->startMinute);
        });
    }

    /**
     * sort Configurations by totalDistance
     * @param RideConfiguration[] $configs
     */
    public static function sortRideConfigurationsByTotalDistance(&$configs) {
        usort($configs, function ($a, $b) {
            return ($a->getTotalDistance() > $b->getTotalDistance());
        });
    }

    /**
     * creates rideNodes from all vehicle depots
     * @param Vehicle[] $vehicles
     */
    private function fillVehicleDepotNodes($vehicles) {
        foreach ($vehicles as $vehicle) {
            $depotAddress = $vehicle->getDepot()->getAddress();
            $this->vehicleDepotAddresses[$depotAddress->getHashFromBigIntCoordinates()] = $depotAddress;
        }
    }

    /**
     * @return RideNode[]
     */
    public function getRideNodes() {
        return $this->rideNodes;
    }

}