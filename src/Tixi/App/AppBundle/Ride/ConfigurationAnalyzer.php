<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:15
 */

namespace Tixi\App\AppBundle\Ride;

use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Vehicle;

/**
 * To Analyze an existing Configuration:
 *  1.  Assign DrivingPools to the RideNodeLists
 *  2.  Assign Vehicles to this RideNodeLists and Pools
 *  3.  If successfully: Assign all Missions from the RideNodes to corresponding Pool
 *
 * Class ConfigurationAnalyzer
 * @package Tixi\App\AppBundle\Ride
 */
class ConfigurationAnalyzer {
    /**@var $rideConfiguration RideConfiguration */
    protected $rideConfiguration;

    /**
     * @param RideConfiguration $rideConfiguration
     */
    public function __construct(RideConfiguration $rideConfiguration) {
        $this->rideConfiguration = $rideConfiguration;
    }

    /**
     * @param RideNode $feasibleNode
     * @return bool
     */
    public function checkIfNodeIsFeasibleInConfiguration(RideNode $feasibleNode) {
        $rideConfiguration = $this->rideConfiguration;
        $addTimePickup = DispositionVariables::ARRIVAL_BEFORE_PICKUP;
        $workRideNodeLists = $rideConfiguration->getRideNodeLists();

        foreach ($rideConfiguration->getDrivingPools() as $drivingPool) {
            if (count($workRideNodeLists) > 0) {
                $rideNodeList = array_shift($workRideNodeLists);
            } else {
                $rideNodeList = new RideNodeList();
                $rideConfiguration->addRideNodeList($rideNodeList);
            }
            if ($rideNodeList->isEmpty()) {
                return true;
            }
            //check if feasibleNode with time constraint fits between existing nodes
            foreach ($rideNodeList->getRideNodes() as $listNode) {
                if (!$listNode->previousNode) {
                    if ($feasibleNode->endMinute + $addTimePickup < $listNode->startMinute) {
                        return true;
                    }
                } else {
                    if ($feasibleNode->endMinute + $addTimePickup < $listNode->startMinute
                        && $feasibleNode->startMinute > $listNode->previousNode->endMinute + $addTimePickup
                    ) {
                        return true;
                    }
                }
                if (!$listNode->nextNode) {
                    if ($feasibleNode->startMinute > $listNode->endMinute + $addTimePickup) {
                        return true;
                    }
                } else {
                    if ($feasibleNode->startMinute > $listNode->endMinute + $addTimePickup
                        && $feasibleNode->endMinute + $addTimePickup < $listNode->nextNode->startMinute
                    ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * assign DrivingPools to a built rideNodeList
     */
    public function assignPoolsToRideNodeList() {
        //TODO: maybe any constraints in future for driving pool to some nodes in this list
        $workPools = $this->rideConfiguration->getDrivingPools();
        /**@var $pool DrivingPool */
        foreach ($this->rideConfiguration->getRideNodeLists() as $list) {
            $pool = array_shift($workPools);

            /*
            if($pool->hasAssociatedDriver()){
                $wh = $pool->getDriver()->getWheelChairAttendance();
                if($list->getMaxWheelChairsOnRide() > 1 && $wh === false){
                    continue;
                }
            }
            */
            $list->setDrivingPoolId($pool->getId());
        }
    }

    /**
     * true if every rideLists get a vehicle assigned
     * @param $vehicles Vehicle[]
     * @return bool
     */
    public function assignVehiclesToBestConfiguration($vehicles) {
        $workVehicles = $vehicles;
        $this->sortVehiclesWithSize($workVehicles);

        $config = $this->rideConfiguration;
        $pools = $config->getDrivingPools();
        $lists = count($config->getRideNodeLists());

        //for all nodelists search a compatible vehicle, continue in vehicle list if not compatible
        foreach ($config->getRideNodeLists() as $nodeList) {
            $poolId = $nodeList->getDrivingPoolId();
            foreach ($workVehicles as $vehicleKey => $vehicle) {
                if ($nodeList->vehicleIsCompatibleWithThisList($vehicle)) {
                    $pool = $pools[$poolId];
                    if ($pool->hasAssociatedDriver()) {
                        if (!$pool->getDriver()->isCompatibleWithVehicleCategory($vehicle->getCategory())) {
                            continue;
                        }
                    }
                    $pools[$poolId]->assignVehicle($vehicle);
                    unset($workVehicles[$vehicleKey]);
                    $lists--;
                    break;
                }
            }
        }
        if ($lists < 1) {
            return true;
        }
        return false;
    }


    /**
     * assign the missions in nodes to every pool <-> nodeList
     */
    public function assignMissionsToPools() {
        $workPools = $this->rideConfiguration->getDrivingPools();
        foreach ($this->rideConfiguration->getRideNodeLists() as $list) {
            $pool = array_shift($workPools);
            foreach ($list->getRideNodes() as $node) {
                $pool->assignDrivingMission($node->drivingMission);
                $node->drivingMission->assignDrivingPool($pool);
            }
        }
    }

    /**
     * @param $vehicles
     */
    private function sortVehiclesWithSize(&$vehicles) {
        usort($vehicles, function ($a, $b) {
            /**@var $a \Tixi\CoreDomain\Vehicle
             * @var $b \Tixi\CoreDomain\Vehicle
             */
            return ($a->getApproximatedSize() > $b->getApproximatedSize());
        });
    }

    /**
     * @param \Tixi\App\AppBundle\Ride\RideConfiguration $rideConfiguration
     */
    public function setRideConfiguration($rideConfiguration) {
        $this->rideConfiguration = $rideConfiguration;
    }

    /**
     * @return \Tixi\App\AppBundle\Ride\RideConfiguration
     */
    public function getRideConfiguration() {
        return $this->rideConfiguration;
    }
}