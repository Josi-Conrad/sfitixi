<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.05.14
 * Time: 18:43
 */

namespace Tixi\App\AppBundle\Ride\RideStrategies;


use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategy;
use Tixi\App\AppBundle\Ride\RideConfiguration;
use Tixi\App\AppBundle\Ride\ConfigurationBuilder;
use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\App\AppBundle\Ride\RideNodeList;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingPool;

/**
 * Class RideStrategyTimeWindow
 * @package Tixi\App\AppBundle\Ride\RideStrategies
 */
class RideStrategyTimeWindow implements RideStrategy {
    /**
     * @var RideNode[] with key = drivingMissionID
     */
    protected $rideNodes;
    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;

    /**
     * @param $rideNodes
     * @param $drivingPools
     * @param $emptyRideNodes
     * @param \Tixi\App\AppBundle\Ride\RideConfiguration $existingConfiguration
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes, RideConfiguration $existingConfiguration = null) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;

        $workNodes = $this->rideNodes;

        //remove existing nodes from workSet
        if ($existingConfiguration) {
            $rideConfiguration = $existingConfiguration;
            foreach ($rideConfiguration->getRideNodeLists() as $list) {
                foreach ($list->getRideNodes() as $key => $node) {
                    unset($workNodes[$key]);
                }
            }
        } else {
            $rideConfiguration = new RideConfiguration($this->drivingPools);
        }

        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        //compare all other RideNodes if they fit in a TimeWindows
        foreach ($rideConfiguration->getRideNodeLists() as $nodeListId => &$nodeList) {
            $lastNodeTime = -1;
            foreach ($workNodes as $nodeKey => $node) {

                /*

                //TODO: compare with existing nodes requires better logic and array_splice to insert node between 2 possible nodes

                if (!$nodeList->isEmpty()) {
                    $isFeasibleToExistingNodes = false;
                    $nodesInList = $nodeList->getRideNodes();
                    foreach ($nodesInList as $key => $existingNode) {
                        if ($node->endMinute < $existingNode->startMinute &&
                            $node->startMinute > $existingNode->endMinute) {
                            $isFeasibleToExistingNodes = true;
                        } else {
                            $isFeasibleToExistingNodes = false;
                            break;
                        }
                    }
                    if ($isFeasibleToExistingNodes) {
                        $lastNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                        $nodeList->addRideNode($node);
                        unset($workNodes[$nodeKey]);
                    }
                    continue;
                }
                */

                if ($node->startMinute > $lastNodeTime) {
                    $nodeList->addRideNode($node);
                    $lastNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;
                    unset($workNodes[$nodeKey]);
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