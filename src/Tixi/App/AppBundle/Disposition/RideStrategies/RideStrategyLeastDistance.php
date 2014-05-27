<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.05.14
 * Time: 18:17
 */

namespace Tixi\App\AppBundle\Disposition\RideStrategies;


use Tixi\App\AppBundle\Disposition\RideConfiguration;
use Tixi\App\AppBundle\Disposition\RideConfigurator;
use Tixi\App\AppBundle\Disposition\RideNode;
use Tixi\App\AppBundle\Disposition\RideNodeList;
use Tixi\App\Disposition\DispositionVariables;

class RideStrategyLeastDistance implements RideStrategy {
    /**
     * @var RideNode[]
     */
    protected $emptyRides;
    protected $rideNodes;
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
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        RideConfigurator::sortNodesByStartMinute($workNodes);

        return $this->buildLeastDistanceConfiguration($workNodes);
    }

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @param $factor
     * @return RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $drivingPools, $emptyRideNodes, $factor) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        RideConfigurator::sortNodesByStartMinute($workNodes);

        //build some configs
        $rideConfigurations = array();

        $c1 = 0;
        $mod = 1;
        $div = count($this->rideNodes);

        for ($i = 0; $i < $div; $i++) {
            $workRideNodes = $workNodes;

            $mod++;
            if ($factor % $mod === 0) {
                $c1++;
            }
            $c2 = $i % $div;
            $switch = $workRideNodes[$c2];
            $workRideNodes[$c2] = $workRideNodes[$c1];
            $workRideNodes[$c1] = $switch;

            $rideConfigurations[] = $this->buildLeastDistanceConfiguration($workRideNodes);
        }
        return $rideConfigurations;
    }

    /**
     * @param $rideNodes
     * @return RideConfiguration
     */
    private function buildLeastDistanceConfiguration($rideNodes) {
        $rideConfiguration = new RideConfiguration();
        $workRideNodes = $rideNodes;

        foreach ($this->drivingPools as $drivingPool) {
            if (count($workRideNodes) < 1) {
                break;
            }
            $rideNodeList = new RideNodeList();

            //set first node on start
            $rideNodeList->addRideNode(array_shift($workRideNodes));
            $copyRideNodes = $workRideNodes;

            $stillFeasible = true;
            while ($stillFeasible) {
                $actualNode = $rideNodeList->getActualRideNode();
                $bestNode = null;
                $bestNodeKey = null;
                $bestEmptyRide = null;
                $actualDistance = 0;

                //check all nodes in workSet for feasible and best distance
                foreach ($copyRideNodes as $compareNodeKey => $compareNode) {
                    //$stillFeasible = false;

                    //not feasible time at all, get next node
                    if (!($actualNode->endMinute < $compareNode->startMinute)) {
                        continue;
                    }

                    $bestEmptyRide = $this->getEmptyRideFromTwoNodes($actualNode, $compareNode);

                    $feasibleTimeForNextNode = $actualNode->endMinute + $bestEmptyRide->duration
                        + DispositionVariables::ARRIVAL_BEFORE_PICKUP;

                    //feasible time for node + emptyRide -> to next node
                    if ($feasibleTimeForNextNode <= $compareNode->startMinute) {
                        //$stillFeasible = true;

                        if ($actualDistance === 0) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $actualDistance = $bestEmptyRide->distance;
                        }

                        //if no node is set, set first and repeat with distance check
                        if ($bestEmptyRide->distance < $actualDistance) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $actualDistance = $bestEmptyRide->distance;
                        }
                    }

                }

                if ($bestNode && $bestEmptyRide) {
                    $copyRideNodes = array_slice($copyRideNodes, $bestNodeKey, count($copyRideNodes));
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
     * @param RideNode $targetNode
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