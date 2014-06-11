<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.05.14
 * Time: 18:17
 */

namespace Tixi\App\AppBundle\Ride\RideStrategies;


use Composer\Config;
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
     * @var $rideNodes RideNode[]
     */
    protected $rideNodes;
    /**
     * @var $emptyRides RideNode[]
     */
    protected $emptyRideNodes;
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
        $this->emptyRideNodes = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);

        $this->adjacenceMatrix = ConfigurationBuilder::buildAdjacenceMatrixFromNodes($workNodes, $emptyRideNodes);

        $initConfig = $this->buildFeasibleConfigFromStrategy(new RideStrategyGenericLeastDistance());
        if ($initConfig) {
            $configurations = $this->annealConfigurations($initConfig);
            //sort and return best configuration
            ConfigurationBuilder::sortRideConfigurationsByDistance($configurations);
            return $configurations[0];
        }
        return null;
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
        $this->emptyRideNodes = $emptyRideNodes;

        $workNodes = $this->rideNodes;
        ConfigurationBuilder::sortNodesByStartMinute($workNodes);
        $this->adjacenceMatrix = ConfigurationBuilder::buildAdjacenceMatrixFromNodes($workNodes, $emptyRideNodes);

        //Initial Config by LeastDistance Strategy
        $s = microtime(true);
        $initConfig = $this->buildFeasibleConfigFromStrategy(new RideStrategyGenericLeastDistance());
        $e = microtime(true);
        echo "Total RideNodes: " . count($this->rideNodes);
        echo "\nFeasible init config built in: " . ($e - $s) . "s\n";

        if ($initConfig) {
            $configurations = $this->annealConfigurations($initConfig);
            return $configurations;
        }
        return null;
    }

    /**
     * build a good and feasible rideConfiguration to anneal from
     * @param RideStrategy $strategy
     * @return RideConfiguration
     */
    private function buildFeasibleConfigFromStrategy(RideStrategy $strategy) {
        $configurations = $strategy->buildConfigurations($this->rideNodes, $this->drivingPools, $this->emptyRideNodes);
        if (count($configurations) > 1) {
            ConfigurationBuilder::removeUnfeasibleConfigurations($configurations);
            if(count($configurations)>1){
                ConfigurationBuilder::sortRideConfigurationsByDistance($configurations);
            } else {
                return null;
            }
            return $configurations[0];
        }
        return null;
    }

    /**
     * Use Simulated Annealing to randomize and optimize an existing feasible Configuration
     * @param $initialConfiguration RideConfiguration
     * @return array
     */
    private function annealConfigurations($initialConfiguration) {
        $configurations = array();
        $initialDistance = $initialConfiguration->getTotalDistance();

        $iteration = -1;
        $temperature = 10000.0;
        $coolingRate = 0.999;
        $absoluteTemperature = 0.0001;

        $distance = $initialDistance;
        $bestDistance = $initialDistance;
        $currentConfiguration = clone $initialConfiguration;

        echo "Annealing with Temperatur: " . $temperature . " and ";
        //loop until system has cooled, this is our break criterion
        while ($temperature > $absoluteTemperature) {
            //swap two nodes
            $nextConfiguration = $this->getNextRandomConfiguration($currentConfiguration);
            if ($nextConfiguration !== null) {
                $nextDistance = $nextConfiguration->getTotalDistance();
                $deltaDistance = $nextDistance - $distance;

                //random next double 0.0 > ran < 1.0
                $ran = mt_rand(1, 100000000) / 100000000;
                //we accept the configuration if distance is lower or satisfies Boltzman condition
                if (($deltaDistance < 0) || ($distance > 0 && exp(-$deltaDistance / $temperature) > $ran)) {
                    $distance = $nextDistance;

                    //keep track of the best configuration
                    if ($distance < $bestDistance) {
                        $bestDistance = $nextDistance;
                        $currentConfiguration = $nextConfiguration;
                        $configurations[] = clone $nextConfiguration;
                    }
                }
            }
            //cool down!
            $temperature *= $coolingRate;
            $iteration++;
        }

        echo $iteration . " Iterations\n";
        echo "Initial Distance: " . $initialDistance / 1000 . "km - ";
        echo "best Annealed Distance: " . $bestDistance / 1000 . "km - ";
        echo "saved " . ($initialDistance - $bestDistance) / 1000 . "km\n";

        return $configurations;
    }

    /**
     * @param RideConfiguration $config
     * @return null|RideConfiguration
     */
    private function getNextRandomConfiguration(RideConfiguration $config) {
        $nextConfig = clone $config;

        //random node from random list
        $l1 = mt_rand(0, count($nextConfig->getRideNodeLists()) - 1);
        $list1 = clone $nextConfig->getRideNodeLists()[$l1];
        $n1 = mt_rand(0, count($list1->getRideNodes()) - 1);
        $node1 = clone $list1->getRideNodes()[$n1];

        $l2 = mt_rand(0, count($nextConfig->getRideNodeLists()) - 1);
        $list2 = clone $nextConfig->getRideNodeLists()[$l2];
        $n2 = mt_rand(0, count($list2->getRideNodes()) - 1);
        $node2 = clone $list2->getRideNodes()[$n2];

        //possible to switch? update all information
        if ($this->isNodeFeasibleToSwitch($node2, $node1) && $this->isNodeFeasibleToSwitch($node1, $node2)) {
            $list1->switchRideNodeAtPosition($n1, $node2);
            $list2->switchRideNodeAtPosition($n2, $node1);
            //update all information
            $this->updateRideNodeListInformation($list1);
            $this->updateRideNodeListInformation($list2);
            $nextConfig->setRideNodeListAt($l1, $list1);
            $nextConfig->setRideNodeListAt($l2, $list2);
            $this->updateRideConfigurationInformation($nextConfig);
        } else {
            return null;
        }
        return $nextConfig;
    }

    /**
     * updates all information in a RideNodeList
     * @param RideNodeList $list
     */
    private function updateRideNodeListInformation(RideNodeList &$list) {
        $totalDistance = 0;
        $totalEmptyRideTime = 0;
        $totalEmptyRideDistance = 0;
        $maxPassengersOnRide = 0;
        $maxWheelChairsOnRide = 0;
        $contradictingVehicleCategories = array();
        foreach ($list->getRideNodes() as $node) {
            $totalDistance += $node->distance;
            if ($node->nextNode) {
                $emptyRide = $this->adjacenceMatrix[$node->getRideNodeHashId()][$node->nextNode->getRideNodeHashId()];
                $totalEmptyRideTime += $emptyRide->duration;
                $totalEmptyRideDistance += $emptyRide->distance;
                $totalDistance += $emptyRide->distance;
            }
            if ($node->passengers > $maxPassengersOnRide) {
                $maxPassengersOnRide += $node->passengers;
            }
            if ($node->wheelChairs > $maxWheelChairsOnRide) {
                $maxWheelChairsOnRide = $node->wheelChairs;
            }
            if (count($node->contradictingVehicleCategories) > 0) {
                foreach ($node->contradictingVehicleCategories as $key => $cat) {
                    $contradictingVehicleCategories[$key] = $cat;
                }
            }
        }
        $list->setTotalDistance($totalDistance);
        $list->setTotalEmptyRideDistance($totalEmptyRideDistance);
        $list->setTotalEmptyRideTime($totalEmptyRideTime);
        $list->setMaxPassengersOnRide($maxPassengersOnRide);
        $list->setMaxWheelChairsOnRide($maxWheelChairsOnRide);
        $list->setContradictingVehicleCategories($contradictingVehicleCategories);
    }

    /**
     * updates all information in a RideConfiguration
     * @param RideConfiguration $config
     */
    private function updateRideConfigurationInformation(RideConfiguration &$config) {
        $totalDistance = 0;
        $totalEmptyRideTime = 0;
        $totalEmptyRideDistance = 0;
        foreach ($config->getRideNodeLists() as $list) {
            $totalDistance += $list->getTotalDistance();
            $totalEmptyRideTime += $list->getTotalEmptyRideTime();
            $totalEmptyRideDistance += $list->getTotalEmptyRideDistance();
        }
        $config->setTotalDistance($totalDistance);
        $config->setTotalEmptyRideDistance($totalEmptyRideDistance);
        $config->setTotalEmptyRideTime($totalEmptyRideTime);
    }

    /**
     * @param $nodeGettingSwitched
     * @param $nodeToSwitch
     * @return array
     */
    private function isNodeFeasibleToSwitch(RideNode $nodeGettingSwitched, RideNode $nodeToSwitch) {
        $left = $nodeGettingSwitched->previousNode;
        $right = $nodeGettingSwitched->nextNode;
        //if previous or next node is null - then we set feas to possible
        if ($left !== null) {
            $feasibleLeft = $this->adjacenceMatrix[$left->getRideNodeHashId()][$nodeToSwitch->getRideNodeHashId()];
        } else {
            $feasibleLeft = 0;
        }
        if ($right !== null) {
            $feasibleRight = $this->adjacenceMatrix[$nodeToSwitch->getRideNodeHashId()][$right->getRideNodeHashId()];
        } else {
            $feasibleRight = 0;
        }
        if (($feasibleLeft !== -1) && ($feasibleRight !== -1)) {
            return true;
        }
        return false;
    }

    /**
     * @param $rideNodes RideNode[]
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
                    //not feasible, get next node
                    $emptyRide = $this->adjacenceMatrix[$actualNode->getRideNodeHashId()][$compareNode->getRideNodeHashId()];
                    if ($emptyRide === -1) {
                        continue;
                    }
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
}