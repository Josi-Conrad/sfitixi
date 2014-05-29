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
use Tixi\CoreDomain\Dispo\DrivingPool;

/**
 * Class RideStrategyAnnealing
 * @package Tixi\App\AppBundle\Ride\RideStrategies
 */
class RideStrategyAnnealing implements RideStrategy {
    /**
     * @var $emptyRides RideNode[]
     */
    protected $emptyRides;
    /**
     * @var $rideNodes RideNode[]
     */
    protected $rideNodes;
    /**
     * @var $drivingPools DrivingPool[]
     */
    protected $drivingPools;

    protected $adjacenceMatrix;

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

        return $this->buildFeasibleConfiguration($workNodes);
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

        $this->adjacenceMatrix = $this->buildAdjacenceMatrixFromNodes($workNodes);
        $currentConfiguration = $this->buildFeasibleConfiguration($workNodes);

        //Annealing
        $iteration = -1;
        $temperature = 10000.0;
        $distance = 0;
        $deltaDistance = 0;
        $coolingRate = 0.9999;
        $absoluteTemperature = 0.00001;

        while ($temperature > $absoluteTemperature) {

            $nextConfiguration = $this->randomSwitchTwoNodes($currentConfiguration);

            $ran = mt_rand($absoluteTemperature * $temperature, $coolingRate * $temperature) / $temperature;
            if (($deltaDistance < 0) || ($distance > 0 && exp(-$deltaDistance / $temperature) > $ran)) {

            }

            $iteration++;
        }

        $rideConfigurations = array();
        for ($i = 0; $i < 2; $i++) {
            $rideConfigurations[] = $this->buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes);
        }
        return $rideConfigurations;
    }


    private function randomSwitchTwoNodes(RideConfiguration $config){
        $nextConfig = $config;

        $l1 = mt_rand(0, count($config->getRideNodeLists())-1);
        $list1 = $config->getRideNodeLists()[$l1];
        $n1 = mt_rand(0, count($list1->getRideNodes())-1);
        $node1 = $list1->getRideNodes()[$n1];

        $l2 = mt_rand(0, count($config->getRideNodeLists())-1);
        $list2 = $config->getRideNodeLists()[$l2];
        $n2 = mt_rand(0, count($list2->getRideNodes())-1);
        $node2 = $list2->getRideNodes()[$n2];

        $feas = $this->adjacenceMatrix[$node1->getRideHash()][$node2->getRideHash()];

        if($feas !== -1 && $feas !== 0){

        } else {
            return null;
        }

        return $nextConfig;
    }

    /**
     * @param $rideNodes
     * @return \Tixi\App\AppBundle\Ride\RideConfiguration
     */
    private function buildFeasibleConfiguration($rideNodes) {
        $rideConfiguration = new RideConfiguration($this->drivingPools);
        $workRideNodes = $rideNodes;

        //always loop all available pools - but stop when no missions are left so it is
        //possible to have empty rideNodeLists (no driver/vehicle needed for these)
        //the amount of created rideNodeLists will give the amount of used vehicles
        foreach ($this->drivingPools as $drivingPool) {
            if (count($workRideNodes) < 1) {
                break;
            }
            $rideNodeList = new RideNodeList();

            //set first node on start
            $rideNodeList->addRideNode(array_shift($workRideNodes));

            $poolExtraMinutesPerRide = 0;
            if ($drivingPool->hasAssociatedDriver()) {
                $poolExtraMinutesPerRide += $drivingPool->getDriver()->getExtraMinutes();
            }

            $stillFeasible = true;
            while ($stillFeasible) {
                $actualNode = $rideNodeList->getActualRideNode();
                $bestNode = null;
                $bestNodeKey = null;
                $bestEmptyRide = null;
                $actualDuration = -1;
                //check all nodes in workSet for feasible and best distance
                foreach ($workRideNodes as $compareNodeKey => $compareNode) {
                    //not feasible time, get next node
                    if (!($actualNode->endMinute < $compareNode->startMinute)) {
                        continue;
                    }
                    $emptyRide = $this->getEmptyRideFromTwoNodes($actualNode, $compareNode);
                    $feasibleTimeForNextNode = $actualNode->endMinute + $emptyRide->duration
                        + DispositionVariables::ARRIVAL_BEFORE_PICKUP + $poolExtraMinutesPerRide;
                    //feasible time for node + emptyRide -> to next node
                    if ($feasibleTimeForNextNode <= $compareNode->startMinute) {
                        if ($actualDuration === -1) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $bestEmptyRide = $emptyRide;
                            $actualDuration = $emptyRide->duration;
                        }
                        if ($emptyRide->duration < $actualDuration) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $bestEmptyRide = $emptyRide;
                            $actualDuration = $emptyRide->duration;
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
            $rideConfiguration->addRideNodeList($rideNodeList);
        }
        $rideConfiguration->setNotFeasibleNodes($workRideNodes);
        $rideConfiguration->removeEmptyRideNodeLists();

        return $rideConfiguration;
    }

    /**
     * @param $rideNodes RideNode[]
     * @return array
     */
    private function buildAdjacenceMatrixFromNodes($rideNodes) {
        $adjacenceMatrix = array();
        foreach ($rideNodes as $leftNode) {
            foreach ($rideNodes as $rightNode) {
                if ($leftNode === $rightNode) {
                    //same node = 0
                    $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = 0;
                    continue;
                }
                if ($leftNode->endMinute < $rightNode->startMinute) {
                    //if our criterium is distance, get this between to nodes
                    $distance = $this->getEmptyRideFromTwoNodes($leftNode, $rightNode)->distance;
                    $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = $distance;
                } else {
                    //not faesible = -1
                    $adjacenceMatrix[$leftNode->getRideHash()][$rightNode->getRideHash()] = -1;
                }
            }
        }
        return $adjacenceMatrix;
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