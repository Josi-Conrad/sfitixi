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
        $this->fillVehicleDepotNodes($vehicles);
        $this->drivingPools = $drivingPools;
        $this->strategy = $rideStrategy;
        $this->emptyRideNodes = array();
        $this->rideConfiguration = new RideConfiguration($drivingPools);
    }

    /**
     * build configurations according to tactic
     * @return RideConfiguration
     */
    public function buildConfiguration() {
        $rideConfiguration = $this->strategy->buildConfiguration($this->rideNodes, $this->drivingPools, $this->emptyRideNodes, $this->rideConfiguration);
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
     * builds array of emptyRides in this builder and returns it
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
     * build rideConfiguration in this builder and returns it
     * @return RideConfiguration
     */
    public function buildConfigurationFromExistingMissions() {
        foreach ($this->drivingPools as $pool) {
            $rideNodeList = new RideNodeList();
            $rideNodeList->assignDrivingPoolToList($pool);
            if ($pool->hasAssociatedDrivingMissions()) {
                $nodes = $this->createRideNodesFromDrivingMissions($pool->getDrivingMissions());
                $this->sortNodesByStartMinute($nodes);
                foreach ($nodes as $node) {
                    $rideNodeList->addRideNode($node);
                }
            }
            $this->rideConfiguration->addRideNodeList($rideNodeList);
        }
        return $this->rideConfiguration;
    }

    /**
     * creates an array with RideNodes according to a drivingMission with missionId as arrayKey
     * @param DrivingMission[] $drivingMissions
     * @return RideNode[] with key = drivingMissionID
     */
    public function createRideNodesFromDrivingMissions($drivingMissions) {
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
                $firstOrder = $drivingMission->getDrivingOrders()->get($sort[$first]);
                /**@var $tOrder DrivingOrder */
                $lastOrder = $drivingMission->getDrivingOrders()->get($sort[$last]);

                if ($drivingMission->getDirection() === DrivingMission::SAME_START) {
                    $startAddress = $firstOrder->getRoute()->getStartAddress();
                    $targetAddress = $lastOrder->getRoute()->getTargetAddress();
                } else {
                    $startAddress = $firstOrder->getRoute()->getStartAddress();
                    $targetAddress = $firstOrder->getRoute()->getTargetAddress();
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
    public static function sortRideConfigurationsByDistance(&$configs) {
        usort($configs, function ($a, $b) {
            return ($a->getTotalDistance() > $b->getTotalDistance());
        });
    }

    /**
     * sort Configurations by totalDistance
     * @param RideConfiguration[] $configs
     */
    public static function sortRideConfigurationsByUsedVehicles(&$configs) {
        usort($configs, function ($a, $b) {
            return ($a->getAmountOfUsedVehicles() > $b->getAmountOfUsedVehicles());
        });
    }

    /**
     * sort Configurations by totalDistance
     * @param RideConfiguration[] $configs
     */
    public static function sortRideConfigurationsByUsedVehicleAndDistance(&$configs) {
        usort($configs, function ($a, $b) {
            return ($a->getAmountOfUsedVehicles() * $a->getTotalDistance() >
                $b->getAmountOfUsedVehicles() * $b->getTotalDistance());
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

    /**
     * @param \Tixi\App\AppBundle\Ride\RideConfiguration $rideConfiguration
     */
    public function setRideConfiguration($rideConfiguration) {
        $this->rideConfiguration = $rideConfiguration;
    }


    /**
     * builds an adjacence matrix (array[][]) for a map between all possible node rides
     * if its not feasible or same node, value is = -1
     * if its feasible, value is = emptyRide between these two nodes
     * @param $rideNodes RideNode[]
     * @param $emptyRideNodes
     * @return array
     */
    public static function buildAdjacenceMatrixFromNodes($rideNodes, $emptyRideNodes) {
        $adjacenceMatrix = array();
        foreach ($rideNodes as $leftNode) {
            foreach ($rideNodes as $rightNode) {
                if ($leftNode === $rightNode) {
                    //same node not feasible
                    $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = -1;
                    continue;
                }
                //feasible nodes in time slice, so get emptyRide between
                if ($leftNode->endMinute < $rightNode->startMinute) {
                    $ride = self::getEmptyRideFromTwoNodes($leftNode, $rightNode, $emptyRideNodes);
                    $feasibleTimeForNextNode = $leftNode->endMinute + $ride->duration + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                    //feasible nodes with emptyRide between
                    if ($feasibleTimeForNextNode <= $rightNode->startMinute) {
                        //if our criteria is distance, get this between two nodes
                        $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = $ride;
                        continue;
                    }
                }
                //not definitly two feasible nodes = -1
                $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = -1;
            }
        }
        return $adjacenceMatrix;
    }

    /**
     * @param RideNode $startNode
     * @param \Tixi\App\AppBundle\Ride\RideNode $targetNode
     * @return string
     */
    public static function getHashFromTwoNodes(RideNode $startNode, RideNode $targetNode) {
        return hash('md2', $startNode->targetAddress->getHashFromBigIntCoordinates()
            . $targetNode->startAddress->getHashFromBigIntCoordinates());
    }

    /**
     * @param RideNode $startNode
     * @param RideNode $targetNode
     * @param $emptyRideNodes
     * @return RideNode
     */
    public static function getEmptyRideFromTwoNodes(RideNode $startNode, RideNode $targetNode, $emptyRideNodes) {
        return $emptyRideNodes[self::getHashFromTwoNodes($startNode, $targetNode)];
    }
}