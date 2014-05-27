<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 17.05.14
 * Time: 10:43
 */

namespace Tixi\App\AppBundle\Disposition;


use Tixi\App\AppBundle\Disposition\RideStrategies\RideStrategy;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Vehicle;

class RideConfigurator {
    /**
     * @var RideStrategies\RideStrategy
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
    protected $emptyRides;
    /**
     * @var Vehicle[]
     */
    protected $availableVehicles;
    /**
     * @var Address[]
     */
    protected $vehicleDepotAddresses;
    /**
     * @var RideConfiguration[]
     */
    protected $rideConfigurations;

    /**
     * @param DrivingMission[] $drivingMissions
     * @param DrivingPool[] $drivingPools
     * @param Vehicle[] $vehicles
     * @param RideStrategies\RideStrategy $rideStrategy
     */
    public function __construct($drivingMissions, $drivingPools, $vehicles, RideStrategy $rideStrategy) {
        $this->rideNodes = $this->createRideNodesFromDrivingMissions($drivingMissions);
        $this->drivingPools = $drivingPools;
        $this->fillVehicleDepotNodes($vehicles);
        $this->strategy = $rideStrategy;
        $this->emptyRides = array();
    }

    /**
     * build configurations according to tactic
     * @return RideConfiguration
     */
    public function buildConfiguration() {
        return $this->strategy->buildConfiguration($this->rideNodes, $this->drivingPools, $this->emptyRides);
    }

    /**
     * @param $factor
     * @return RideConfiguration[]
     */
    public function buildConfigurations($factor) {
        return $this->strategy->buildConfigurations($this->rideNodes, $this->drivingPools, $this->emptyRides, $factor);
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
                $this->emptyRides[$depotToNode->getRideHash()] = $depotToNode;
                $this->emptyRides[$nodeToDepot->getRideHash()] = $nodeToDepot;
            }

            //fill possible rides between any time-feasible nodes
            $comparesNodes = $workNodes;
            foreach ($comparesNodes as $compareNode) {
                if ($workNode->endMinute < $compareNode->startMinute) {
                    $node = RideNode::registerEmptyRide($workNode->targetAddress, $compareNode->startAddress);
                    $this->emptyRides[$node->getRideHash()] = $node;
                }
            }
            unset($workNodes[$key]);
        }
        return $this->emptyRides;
    }

    /**
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
        return $this->emptyRides;
    }

    /**
     * @return RideConfiguration[]
     */
    public function getRideConfigurations() {
        return $this->rideConfigurations;
    }

    /**
     * sort Mission by startMinutes
     */
    public static function sortNodesByStartMinute(&$nodes) {
        usort($nodes, function ($a, $b) {
            return ($a->startMinute > $b->startMinute);
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