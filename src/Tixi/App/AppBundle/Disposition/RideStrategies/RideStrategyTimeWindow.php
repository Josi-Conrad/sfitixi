<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.05.14
 * Time: 18:43
 */

namespace Tixi\App\AppBundle\Disposition\RideStrategies;


use Tixi\App\AppBundle\Disposition\RideConfiguration;
use Tixi\App\AppBundle\Disposition\RideConfigurator;
use Tixi\App\AppBundle\Disposition\RideNode;
use Tixi\App\AppBundle\Disposition\RideNodeList;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingPool;

class RideStrategyTimeWindow implements RideStrategy {
    /**
     * @var RideNode[]
     */
    protected $rideNodes;
    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;

        $rideConfiguration = new RideConfiguration();
        $workNodes = $this->rideNodes;

        //fill existing missions<->orders and nodes away from workNodes
        foreach ($this->drivingPools as $drivingPool) {
            /**@var $drivingPool DrivingPool */
            if ($drivingPool->hasAssociatedDrivingMissions()) {
                $rideNodeList = new RideNodeList();
                foreach ($drivingPool->getDrivingMissions() as $mission) {
                    $id = $mission->getId();
                    if ($workNodes[$id] !== null) {
                        $rideNodeList->addRideNode($workNodes[$id]);
                        unset($workNodes[$id]);
                    }
                }
                $rideConfiguration->addRideNodeListAtPool($drivingPool, $rideNodeList);
            }
        }

        RideConfigurator::sortNodesByStartMinute($workNodes);

        //compare all other RideNodes if they fit in a TimeWindows
        foreach ($rideConfiguration->getRideNodeLists() as $poolId => $poolNodes) {
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
                        $rideConfiguration->getRideNodeListFromPoolId($poolId)[$missionId] = $node;
                        $lastNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                        unset($workNodes[$missionId]);
                    }
                }
            }
        }

        //left Nodes are not feasible
        if (count($workNodes) > 1) {
            echo "\nNot feasible Nodes " . count($workNodes) . "\n";
            $rideConfiguration->setNotFeasibleNodes($workNodes);
        }

        return $rideConfiguration;
    }

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @param $factor
     * @return RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $emptyRideNodes, $drivingPools, $factor) {
        return $this->buildConfiguration($rideNodes, $emptyRideNodes, $drivingPools);
    }
}