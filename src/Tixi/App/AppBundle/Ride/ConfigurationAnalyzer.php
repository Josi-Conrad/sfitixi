<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:15
 */

namespace Tixi\App\AppBundle\Ride;

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
     * @param RideNode $rideNode
     * @return bool
     */
    public function checkIfNodeIsFeasibleInConfiguration(RideNode $rideNode) {
        $feasible = false;
        foreach ($this->rideConfiguration->getRideNodeLists() as $rideNodeList) {
            /**@var RideNode $lastNode */
            $lastNode = null;
            foreach ($rideNodeList->getRideNodes() as $node) {
                if ($lastNode === null) {
                    $lastNode = $node;
                    if ($rideNode->endMinute < $lastNode->startMinute) {
                        $feasible = true;
                    }
                    continue;
                }

                if ($rideNode->startMinute < $lastNode->endMinute && $rideNode->endMinute < $node->startMinute
                    || $rideNode->endMinute < $lastNode->startMinute
                ) {
                    $feasible = true;
                }
                $lastNode = $node;
            }

            if ($rideNode->startMinute > $lastNode->endMinute) {
                $feasible = true;
            }
        }
        return $feasible;
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

        foreach ($config->getRideNodeLists() as $poolId => $nodeList) {
            foreach ($workVehicles as $vehicleKey => $vehicle) {
                if ($nodeList->vehicleIsCompatibleWithThisList($vehicle)) {
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