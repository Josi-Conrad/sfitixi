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
     * @var $rideNodes RideNode[]
     */
    protected $rideNodes;
    /**
     * @var $drivingPools DrivingPool[]
     */
    protected $drivingPools;

    /**
     * this adds all missions to existing configuration with compare to a time slice window,
     * no exact routing informations given
     * @param $rideNodes
     * @param $drivingPools
     * @param $emptyRideNodes
     * @param \Tixi\App\AppBundle\Ride\RideConfiguration $existingConfiguration
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes, RideConfiguration $existingConfiguration = null) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;

        /**@var $workRideNodes RideNode[] */
        $workRideNodes = $this->rideNodes;
        $addTimePickup = DispositionVariables::ARRIVAL_BEFORE_PICKUP;

        //remove existing nodes from workSet
        if ($existingConfiguration) {
            $rideConfiguration = $existingConfiguration;
            foreach ($rideConfiguration->getRideNodeLists() as $list) {
                foreach ($list->getRideNodes() as $key => $node) {
                    unset($workRideNodes[$key]);
                }
            }
        } else {
            $rideConfiguration = new RideConfiguration($this->drivingPools);
        }

        ConfigurationBuilder::sortNodesByStartMinute($workRideNodes);

        $workRideNodeLists = $rideConfiguration->getRideNodeLists();

        //always loop all available pools - but stop when no missions are left so it is
        //possible to have empty pools (no driver/vehicle needed for these missions)
        foreach ($drivingPools as $drivingPool) {
            if (count($workRideNodes) < 1) {
                break;
            }
            if (count($workRideNodeLists) > 0) {
                $rideNodeList = array_shift($workRideNodeLists);
            } else {
                $rideNodeList = new RideNodeList();
                $rideConfiguration->addRideNodeList($rideNodeList);
            }

            //no existing list with nodes, so we add rideNodes normally with time constraint
            if ($rideNodeList->isEmpty()) {
                $rideNodeList->addRideNode(array_shift($workRideNodes));
                $actualNode = $rideNodeList->getActualRideNode();
                foreach ($workRideNodes as $nodeKey => $node) {
                    if ($node->startMinute > $actualNode->endMinute + $addTimePickup) {
                        $actualNode = $node;
                        $rideNodeList->addRideNode($node);
                        unset($workRideNodes[$nodeKey]);
                    }
                }
            } else {
                //existing list with nodes, add rideNodes with time constraint between existing nodes
                foreach ($rideNodeList->getRideNodes() as $listNode) {
                    foreach ($workRideNodes as $nodeKey => $node) {
                        if (!$listNode->previousNode) {
                            if ($node->endMinute + $addTimePickup < $listNode->startMinute) {
                                $rideNodeList->addRideNodeBeforeRideNode($node, $listNode);
                                unset($workRideNodes[$nodeKey]);
                                continue;
                            }
                        } else {
                            if ($node->endMinute + $addTimePickup < $listNode->startMinute
                                && $node->startMinute > $listNode->previousNode->endMinute + $addTimePickup
                            ) {
                                $rideNodeList->addRideNodeBeforeRideNode($node, $listNode);
                                unset($workRideNodes[$nodeKey]);
                                continue;
                            }
                        }

                        if (!$listNode->nextNode) {
                            if ($node->startMinute > $listNode->endMinute + $addTimePickup) {
                                $rideNodeList->addRideNodeAfterRideNode($node, $listNode);
                                unset($workRideNodes[$nodeKey]);
                                continue;
                            }
                        } else {
                            if ($node->startMinute > $listNode->endMinute + $addTimePickup
                                && $node->endMinute + $addTimePickup < $listNode->nextNode->startMinute
                            ) {
                                $rideNodeList->addRideNodeAfterRideNode($node, $listNode);
                                unset($workRideNodes[$nodeKey]);
                                continue;
                            }
                        }
                    }
                }
            }
        }

        //left Nodes are not feasible
        if (count($workRideNodes) > 1) {
            echo "\nNot feasible Nodes " . count($workRideNodes) . "\n";
            $rideConfiguration->setNotFeasibleNodes($workRideNodes);
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