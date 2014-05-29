<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.05.14
 * Time: 18:17
 */

namespace Tixi\App\AppBundle\Ride\RideStrategies;


use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategy;
use Tixi\App\AppBundle\Ride\RideConfiguration;
use Tixi\App\AppBundle\Ride\ConfigurationBuilder;
use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\App\AppBundle\Ride\RideNodeList;
use Tixi\App\Disposition\DispositionVariables;

/**
 * Class RideStrategyLeastDistance
 * @package Tixi\App\AppBundle\Ride\RideStrategies
 */
class RideStrategyLeastDistance implements RideStrategy {
    /**
     * @var RideNode[]
     */
    protected $emptyRides;
    protected $rideNodes;
    protected $drivingPools;

    /**
     * @param $rideNodes
     * @param $drivingPools
     * @param $emptyRideNodes
     * @param \Tixi\App\AppBundle\Ride\RideConfiguration $existingConfiguration
     * @return \Tixi\App\AppBundle\Ride\RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes, RideConfiguration $existingConfiguration = null) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        return $this->buildLeastDistanceConfiguration($workNodes);
    }

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @param $factor
     * @return \Tixi\App\AppBundle\Ride\RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $drivingPools, $emptyRideNodes, $factor) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        //build some configs and return feasible configs
        $rideConfigurations = array();
        $diversity = count($this->rideNodes);
        for ($i = 0; $i < $factor; $i++) {
            $workRideNodes = $workNodes;
            if ($i >= $diversity) {
                shuffle($workRideNodes);
            } else {
                $switch = $workRideNodes[0];
                $workRideNodes[0] = $workRideNodes[$i];
                $workRideNodes[$i] = $switch;
            }

            $rideConfiguration = $this->buildLeastDistanceConfiguration($workRideNodes);

            if (!$rideConfiguration->hasNotFeasibleNodes()) {
                $rideConfigurations[] = $rideConfiguration;
            }
        }

        ConfigurationBuilder::sortRideConfigurationsByTotalDistance($rideConfigurations);
        return $rideConfigurations;
    }

    /**
     * @param $rideNodes
     * @return \Tixi\App\AppBundle\Ride\RideConfiguration
     */
    private function buildLeastDistanceConfiguration($rideNodes) {
        $rideConfiguration = new RideConfiguration($this->drivingPools);
        $workRideNodes = $rideNodes;

        foreach ($this->drivingPools as $drivingPool) {
            if (count($workRideNodes) < 1) {
                break;
            }
            $rideNodeList = new RideNodeList();

            //set first node on start
            $rideNodeList->addRideNode(array_shift($workRideNodes));

            $stillFeasible = true;
            while ($stillFeasible) {
                $actualNode = $rideNodeList->getActualRideNode();
                $bestNode = null;
                $bestNodeKey = null;
                $bestEmptyRide = null;
                $actualDistance = -1;

                //check all nodes in workSet for feasible and best distance
                foreach ($workRideNodes as $compareNodeKey => $compareNode) {

                    //not feasible time, get next node
                    if (!($actualNode->endMinute < $compareNode->startMinute)) {
                        continue;
                    }

                    $emptyRide = $this->getEmptyRideFromTwoNodes($actualNode, $compareNode);

                    $feasibleTimeForNextNode = $actualNode->endMinute + $emptyRide->duration
                        + DispositionVariables::ARRIVAL_BEFORE_PICKUP;

                    //feasible time for node + emptyRide -> to next node
                    if ($feasibleTimeForNextNode <= $compareNode->startMinute) {

                        if ($actualDistance === -1) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $bestEmptyRide = $emptyRide;
                            $actualDistance = $emptyRide->distance;
                        }

                        //if no node is set, set first and repeat with distance check
                        if ($emptyRide->distance < $actualDistance) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $bestEmptyRide = $emptyRide;
                            $actualDistance = $emptyRide->distance;
                        }
                    }

                }

                if ($bestNode && $bestEmptyRide) {
                    $rideNodeList->addRideNode($bestNode);
                    $rideNodeList->addRideNode($bestEmptyRide);
                    unset($workRideNodes[$bestNodeKey]);
                } else {
                    $stillFeasible = false;
                }
            }
            $rideConfiguration->addRideNodeListAtPool($drivingPool, $rideNodeList);
        }
        $rideConfiguration->setNotFeasibleNodes($workRideNodes);
        return $rideConfiguration;
    }

    /**
     * @param RideNode $startNode
     * @param \Tixi\App\AppBundle\Ride\RideNode $targetNode
     * @return string
     */
    private function getHashFromTwoNodes(RideNode $startNode, RideNode $targetNode) {
        return hash('md2', $startNode->targetAddress->getHashFromBigIntCoordinates()
            . $targetNode->startAddress->getHashFromBigIntCoordinates());
    }

    /**
     * @param RideNode $startNode
     * @param RideNode $targetNode
     * @return RideNode
     */
    private function getEmptyRideFromTwoNodes(RideNode $startNode, RideNode $targetNode) {
        return $this->emptyRides[$this->getHashFromTwoNodes($startNode, $targetNode)];
    }
}