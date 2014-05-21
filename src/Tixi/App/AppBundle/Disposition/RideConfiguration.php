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
     * @var
     */
    protected $type;
    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;
    /**
     * @var RideNode[]
     */
    protected $missionNodes;
    /**
     * @var
     */
    protected $notFeasibleNodes;

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
        $this->type = $type;
    }

    public function buildConfiguration() {
        switch ($this->type) {
            case self::ONLY_TIME_WINDOWS:
                $this->buildTimeWindowsConfiguration();
                break;
            case self::LEAST_KILOMETER:
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
    public function getAllPossibleEmptyRides() {
        $this->sortNodesByStartMinute($this->missionNodes);
        $emptyRides = array();

        $workNodes = $this->missionNodes;
        foreach ($workNodes as $key => $workNode) {
            $comparesNodes = $workNodes;
            foreach ($comparesNodes as $compareNode) {
                if ($workNode->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP < $compareNode->startMinute) {
                    $node = RideNode::registerEmptyRide($workNode->targetAddress, $compareNode->startAddress);
                    $emptyRides[$node->getRideHash()] = $node;
                }
            }
            unset($workNodes[$key]);
        }
        return $emptyRides;
    }

    /**
     * Build configuration according to RideNode times and DrivingPools
     */
    protected function buildTimeWindowsConfiguration() {
        //array copy = not copy/clone values, only references
        //so we can edit the references without impact values
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
     * @param RideNode $feasibleNode
     */
    public function addAdditionalRideNode(RideNode $feasibleNode) {
        $this->missionNodes[] = $feasibleNode;
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
            $missionNodes[$drivingMission->getId()] = $missionNode;
        }
        return $missionNodes;
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
     * @return bool
     */
    public function gotNotFeasibleNodes() {
        return isset($this->notFeasibleNodes);
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


}