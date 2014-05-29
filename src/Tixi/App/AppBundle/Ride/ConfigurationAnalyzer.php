<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:15
 */

namespace Tixi\App\AppBundle\Ride;

use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Vehicle;

/**
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

        foreach ($rideConfiguration->getDrivingPools() as $drivingPool) {
            $rideNodeList = $rideConfiguration->getRideNodeListForPool($drivingPool);

            //if List is empty, there is a drivingPool without nodes, so its definitely possible to drive this mission
            if ($rideNodeList === null) {
                return true;
            }
            if($rideNodeList->isEmpty()){
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
        foreach ($config->getRideNodeLists() as $poolId => $nodeList) {
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
}