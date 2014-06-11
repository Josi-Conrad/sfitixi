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
 * Class RideStrategyLeastDistance
 * @package Tixi\App\AppBundle\Ride\RideStrategies
 */
class RideStrategyLeastDistance implements RideStrategy {
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
    /**
     * @var array[][]
     */
    protected $adjacenceMatrix;

    /**
     * @param $rideNodes
     * @param $drivingPools
     * @param $emptyRideNodes
     * @param RideConfiguration $existingConfiguration
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes, RideConfiguration $existingConfiguration = null) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        $this->adjacenceMatrix = ConfigurationBuilder::buildAdjacenceMatrixFromNodes($workNodes, $emptyRideNodes);

        return $this->buildGenericLeastDistanceConfiguration($workNodes);
    }

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @return RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $drivingPools, $emptyRideNodes) {
        $this->rideNodes = $rideNodes;
        $this->drivingPools = $drivingPools;
        $this->emptyRides = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        $this->adjacenceMatrix = ConfigurationBuilder::buildAdjacenceMatrixFromNodes($workNodes, $emptyRideNodes);

        //build some different configurations
        $rideConfigurations = array();
        $diversity = count($this->drivingPools);
        $factor = $diversity * 2;

        //take for each driving pool another start node, and then shuffle the whole node array some times
        //to create another start set to begin with
        for ($i = 0; $i < $factor; $i++) {
            $workRideNodes = $workNodes;
            if ($i > $diversity) {
                shuffle($workRideNodes);
            } else {
                $switch = $workRideNodes[0];
                $workRideNodes[0] = $workRideNodes[$i];
                $workRideNodes[$i] = $switch;
            }

            $rideConfigurations[] = $this->buildGenericLeastDistanceConfiguration($workRideNodes);
        }
        return $rideConfigurations;
    }

    /**
     * @param $rideNodes RideNode[]
     * @return RideConfiguration
     */
    private function buildGenericLeastDistanceConfiguration($rideNodes) {
        $rideConfiguration = new RideConfiguration($this->drivingPools);
        $workRideNodes = $rideNodes;

        //always loop all available pools - but stop when no missions are left so it is
        //possible to have empty pools (no driver/vehicle needed for these missions)
        //the amount of creates rideNodeLists will give the amount of used vehicles
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
                $actualDistance = -1;

                //check all nodes in workSet for feasible and best distance
                foreach ($workRideNodes as $compareNodeKey => $compareNode) {
                    /**@var RideNode $emptyRide */
                    $emptyRide = $this->adjacenceMatrix[$actualNode->getRideNodeHashId()][$compareNode->getRideNodeHashId()];
                    //not feasible, get next node
                    if ($emptyRide === -1) {
                        continue;
                    }
                    $feasibleTimeForNextNode = $actualNode->endMinute + $emptyRide->duration
                        + DispositionVariables::ARRIVAL_BEFORE_PICKUP + $poolExtraMinutesPerRide;
                    //feasible time for node + emptyRide -> to next node
                    if ($feasibleTimeForNextNode <= $compareNode->startMinute) {
                        if ($actualDistance === -1) {
                            $bestNode = $compareNode;
                            $bestNodeKey = $compareNodeKey;
                            $bestEmptyRide = $emptyRide;
                            $actualDistance = $emptyRide->distance;
                        }
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
            $rideConfiguration->addRideNodeList($rideNodeList);
        }
        $rideConfiguration->setNotFeasibleNodes($workRideNodes);
        return $rideConfiguration;
    }
}