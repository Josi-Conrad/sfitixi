<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 17.05.14
 * Time: 10:43
 */

namespace Tixi\App\AppBundle\Disposition;


use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;

class RideConfiguration {
    /**
     * only for simple feasibility check
     */
    const ONLY_TIME_WINDOWS = 0;

    /** TACTICS
     * from vehicle all possible missions to next vehicle
     */
    const LEAST_VEHICLE = 1;
    /**
     * choose corresponding missions with least EMPTY_DRIVE kilometers
     */
    const LEAST_KILOMETER = 2;
    /**
     * try to do maximum possible missions (=least_vehicle?)
     */
    const MOST_POSSIBLE_MISSIONS = 3;

    /**
     * @var
     */
    protected $type;
    /**
     * @var DrivingPool[]
     */
    protected $drivingPools;
    /**
     * @var RideNode[]
     */
    protected $missionNodes;
    /**
     * @var
     */
    protected $notFeasibleNodes;

    /**
     * two dimensional array of missions according to a pool
     * @var
     */
    protected $rideConfiguration;

    public static $configurationTactics = array(
        self::LEAST_VEHICLE,
        self::LEAST_KILOMETER,
        self::MOST_POSSIBLE_MISSIONS
    );

    public function __construct($missionNodes, $drivingPools, $type = self::ONLY_TIME_WINDOWS) {
        $this->missionNodes = $missionNodes;
        $this->drivingPools = $drivingPools;
        $this->rideConfiguration = array();
        $this->type = $type;
    }

    public function buildConfiguration() {
        switch ($this->type) {
            case self::ONLY_TIME_WINDOWS:
                $this->buildTimeWindowsConfiguration();
                break;
            case self::LEAST_KILOMETER:
                break;
            case self::LEAST_VEHICLE:
                break;
            case self::MOST_POSSIBLE_MISSIONS:
                break;
        }
    }

    /**
     * @return array
     */
    public function getAllPossibleEmptyRides() {
        $this->sortNodesByStartMinute();
        $emptyRides = array();

        $workNodes = $this->missionNodes;
        foreach ($workNodes as $key => $workNode) {
            $comparesNodes = $workNodes;
            foreach ($comparesNodes as $cNode) {
                if ($workNode->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP < $cNode->startMinute) {
                    array_push($emptyRides,
                        RideNode::registerEmptyRide($workNode->endAddress, $cNode->startAddress));
                }
            }
            unset($workNodes[$key]);
        }
        return $emptyRides;
    }

    protected function buildTimeWindowsConfiguration() {
        $this->sortNodesByStartMinute();

        //array copy, does not copy/clone values, only references
        $workNodes = $this->missionNodes;

        //amount of DrivingPools = drivers = vehicles needed for this shift
        foreach ($this->drivingPools as $pool => $poolNodes) {
            $poolNodesArray = array();
            array_push($this->rideConfiguration, $pool);
            $nextNodeTime = 0;
            foreach ($workNodes as $key => $node) {
                if ($node->startMinute > $nextNodeTime) {
                    array_push($poolNodesArray, $node);
                    //exact way: endTime + emptyNode duration + arrival_before_pickup
                    $nextNodeTime = $node->endMinute + DispositionVariables::ARRIVAL_BEFORE_PICKUP;

                    //unset key (not affect value)
                    unset($workNodes[$key]);
                }
            }
            $this->rideConfiguration[$pool] = $poolNodesArray;
        }

        //left Nodes are not feasible
        if (count($workNodes) > 1) {
            $this->notFeasibleNodes = array();
            foreach ($workNodes as $notFeasibleNode) {
                array_push($this->notFeasibleNodes, $notFeasibleNode);
            }
        }
    }

    /**
     * @return bool
     */
    public function gotNotFeasibleNodes() {
        return isset($this->notFeasibleNodes);
    }

    /**
     * sort Mission by startMinutes
     */
    private function sortNodesByStartMinute() {
        usort($this->missionNodes, function ($a, $b) {
            return ($a->startMinute > $b->startMinute);
        });
    }
} 