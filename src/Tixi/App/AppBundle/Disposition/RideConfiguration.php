<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 17.05.14
 * Time: 10:43
 */

namespace Tixi\App\AppBundle\Disposition;


use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Vehicle;

class RideConfiguration {
    /**
     * only for simple feasibility check
     */
    const ONLY_TIME_WINDOWS = 0;
    /** TACTICS
     * from vehicle all possible missions to next vehicle
     */
    const LEAST_VEHICLE = 1;
    /**
     * choose corresponding missions with least EMPTY_DRIVE kilometers
     */
    const LEAST_KILOMETER = 2;
    /**
     * try to do maximum possible missions (=least_vehicle?)
     */
    const MOST_POSSIBLE_MISSIONS = 3;

    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;
    /**
     * @var RideNode[]
     */
    protected $missionNodes;
    /**
     * @var RideNode[]
     */
    protected $notFeasibleNodes;
    /**
     * @var RideNode[]
     */
    protected $emptyRides;
    /**
     * @var Vehicle[]
     */
    protected $availableVehicles;
    protected $vehicleDepotAddresses;
    protected $type;
    protected $totalDistance;

    /**
     * two dimensional array of missionNodes according to a pool
     * @var array[drivingPoolId][RideNode[]]
     */
    protected $rideConfiguration;

    public static $configurationTactics = array(
        self::LEAST_VEHICLE,
        self::LEAST_KILOMETER,
        self::MOST_POSSIBLE_MISSIONS
    );

    /**
     * @param DrivingMission[] $drivingMissions
     * @param DrivingPool[] $drivingPools
     * @param Vehicle[] $vehicles
     * @param int $type
     * @internal param \Tixi\App\AppBundle\Disposition\RideNode[] $missionNodes
     */
    public function __construct($drivingMissions, $drivingPools, $vehicles, $type = self::ONLY_TIME_WINDOWS) {
        $this->missionNodes = $this->createPassengerRideNodesFromDrivingMissions($drivingMissions);
        foreach ($drivingPools as $pool) {
            $id = $pool->getId();
            $this->drivingPools[$id] = $pool;
            $this->rideConfiguration[$id] = array();
        }
        $this->emptyRides = array();
        $this->availableVehicles = $vehicles;
        $this->fillVehicleDepotNodes();
        $this->type = $type;
    }

    public function buildConfiguration() {
        switch ($this->type) {
            case self::ONLY_TIME_WINDOWS:
                $this->buildTimeWindowsConfiguration();
                break;
            case self::LEAST_KILOMETER:
                $this->buildLeastKilometerConfiguration();
                break;
            case self::LEAST_VEHICLE:
                break;
            case self::MOST_POSSIBLE_MISSIONS:
                break;
        }
    }

    /**
     * gets hash array with nodes, where key is concatenated coordinate hash (md2) from start and destination
     * @return array
     */
    public function buildAllPossibleEmptyRides() {
        $this->sortNodesByStartMinute($this->missionNodes);

        $workNodes = $this->missionNodes;
        foreach ($workNodes as $key => $workNode) {
            //fill rides from and to vehicleDepot
            foreach ($this->vehicleDepotAddresses as $depotAddress) {
                $depotToNode = RideNode::registerEmptyRide($depotAddress, $workNode->startAddress);
                $nodeToDepot = RideNode::registerEmptyRide($workNode->targetAddress, $depotAddress);
                $this->emptyRides[$depotToNode->getRideHash()] = $depotToNode;
                $this->emptyRides[$nodeToDepot->getRideHash()] = $nodeToDepot;
            }

            //fill possible rides between any nodes
            $comparesNodes = $workNodes;
            foreach ($comparesNodes as $compareNode) {
                if ($workNode->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP < $compareNode->startMinute) {
                    $node = RideNode::registerEmptyRide($workNode->targetAddress, $compareNode->startAddress);
                    $this->emptyRides[$node->getRideHash()] = $node;
                }
            }
            unset($workNodes[$key]);
        }
        return $this->emptyRides;
    }

    protected function buildLeastKilometerConfiguration() {
        $workNodes = $this->missionNodes;
        $workVehicles = $this->availableVehicles;
        $this->sortNodesByStartMinute($workNodes);

        foreach ($this->drivingPools as $poolId => $pool) {

            $this->rideConfiguration[$poolId] = new \SplDoublyLinkedList();
            if ($pool->hasAssociatedDriver()) {
                foreach ($workVehicles as $vehicleKey => $vehicle) {
                    $vehicleCategory = $vehicle->getCategory();
                    $depot = $vehicle->getDepot()->getAddress();
                    $depotNode = RideNode::registerEmptyRide($depot, $depot);
                    /**@var $list \SplDoublyLinkedList */
                    $list = $this->rideConfiguration[$poolId];

                    if ($list->isEmpty()) {
                        $list->push($depotNode);
                    }

                    if ($pool->getDriver()->isCompatibleWithVehicleCategory($vehicleCategory)) {
                        /**@var $bestNode RideNode */
                        $bestNode = null;
                        $bestNodeKey = 0;
                        $bestNodeDistance = 0;

                        foreach ($workNodes as $nodeKey => $nextNode) {
                            if ($nextNode->drivingMission->isCompatibleWithVehicleCategory($vehicleCategory)) {
                                /**@var $currentNode RideNode */
//                                $currentNode = $list->pop();
                                $currentNode = $depotNode;

                                $emptyRide = $this->emptyRides[$this->getHashFromTwoNodes($currentNode, $nextNode)];

                                if ($bestNode !== null) {
                                    //is possible in time
                                    if ($currentNode->endMinute + $emptyRide->duration < $nextNode->startMinute) {
                                        //distance is better then previous node
                                        if ($emptyRide->distance < $bestNodeDistance) {
                                            $bestNode = $nextNode;
                                            $bestNodeKey = $nodeKey;
                                            $bestNodeDistance = $emptyRide->distance;
                                        }
                                    }
                                } else {
                                    $bestNode = $nextNode;
                                    $bestNodeKey = $nodeKey;
                                    $bestNodeDistance = $emptyRide->distance;
                                }
                            }
                        }
                        unset($workNodes[$bestNodeKey]);
                    }
                }
            }
        }

    }

    protected function findNearestNextNode($startNode, $nodes) {

    }

    /**
     * Build configuration according to RideNode times and DrivingPools
     */
    protected function buildTimeWindowsConfiguration() {
        $workNodes = $this->missionNodes;

        //fill existing missions<->orders and nodes away from workNodes
        foreach ($this->rideConfiguration as $poolId => $poolNodes) {
            $drivingPool = $this->drivingPools[$poolId];
            if ($drivingPool->hasAssociatedDrivingMissions()) {
                foreach ($drivingPool->getDrivingMissions() as $mission) {
                    $id = $mission->getId();
                    if ($workNodes[$id] !== null) {
                        $poolNodes[$id] = $workNodes[$id];
                        unset($workNodes[$id]);
                    }
                }
            }
        }

        $this->sortNodesByStartMinute($workNodes);

        //compare all other RideNodes if they fit in a TimeWindows
        foreach ($this->rideConfiguration as $poolId => $poolNodes) {
            $lastNodeTime = 0;
            foreach ($workNodes as $missionId => $node) {
                //compare with existing nodes
                if (count($poolNodes) > 0) {
                    foreach ($poolNodes as $key => $poolNode) {
                        if ($node->endMinute < $poolNode->startMinute ||
                            $node->startMinute > $poolNode->endMinute
                        ) {
                            $poolNodes[$missionId] = $node;
                            unset($workNodes[$missionId]);
                        }
                    }
                    $lastNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                } else {
                    if ($node->startMinute > $lastNodeTime) {
                        $this->rideConfiguration[$poolId][$missionId] = $node;
                        $lastNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                        unset($workNodes[$missionId]);
                    }
                }
            }
        }

        //left Nodes are not feasible
        if (count($workNodes) > 1) {
            $this->notFeasibleNodes = array();
            foreach ($workNodes as $missionId => $notFeasibleNode) {
                $this->notFeasibleNodes[$missionId] = $notFeasibleNode;
            }
        }
        echo "\nNot feasible Nodes " . count($this->notFeasibleNodes) . "\n";
    }

    /**
     * @param DrivingMission[] $drivingMissions
     * @return RideNode[]
     */
    private function createPassengerRideNodesFromDrivingMissions($drivingMissions) {
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
        $this->missionNodes[] = $feasibleNode;
    }

    /**
     * @return array
     */
    public function getEmptyRides() {
        return $this->emptyRides;
    }

    /**
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getRideConfigurationArray() {
        return $this->rideConfiguration;
    }

    /**
     * sort Mission by startMinutes
     */
    private function sortNodesByStartMinute(&$nodes) {
        usort($nodes, function ($a, $b) {
            return ($a->startMinute > $b->startMinute);
        });
    }

    /**
     * creates rideNodes from all vehicle depots
     */
    private function fillVehicleDepotNodes() {
        foreach ($this->availableVehicles as $vehicle) {
            $depotAddress = $vehicle->getDepot()->getAddress();
            $this->vehicleDepotAddresses[$depotAddress->getHashFromBigIntCoordinates()] = $depotAddress;
        }
    }

    /**
     * @return bool
     */
    public function gotNotFeasibleNodes() {
        return isset($this->notFeasibleNodes);
    }

    /**
     * @param RideNode $startNode
     * @param RideNode $targetNode
     * @return string
     */
    private function getHashFromTwoNodes(RideNode $startNode, RideNode $targetNode) {
        return hash('md2', $startNode->targetAddress->getHashFromBigIntCoordinates()
            . $targetNode->startAddress->getHashFromBigIntCoordinates());
    }
}