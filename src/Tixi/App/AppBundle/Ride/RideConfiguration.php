<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:26
 */

namespace Tixi\App\AppBundle\Ride;


use Tixi\App\AppBundle\Ride\RideNodeList;
use Tixi\CoreDomain\Dispo\DrivingPool;

/**
 * Class RideConfiguration
 * @package Tixi\App\AppBundle\Ride
 */
class RideConfiguration {
    /**
     * time above all empty rides
     * @var int
     */
    protected $totalEmptyRideTime;
    /**
     * distance above all empty rides
     * @var int
     */
    protected $totalEmptyRideDistance;
    /**
     * total distance from all lists
     * @var int
     */
    protected $totalDistance;
    /**
     * any nodes that are left on this configuration = not feasible
     * @var $notFeasibleNodes RideNode[]
     */
    protected $notFeasibleNodes;

    /**
     * Array of RideNodeLists
     * @var $rideNodeLists RideNodeList[]
     */
    protected $rideNodeLists;

    /**
     * Array of DrivingPools with key = drivingPoolID
     * @var $drivingPools DrivingPool[]
     */
    protected $drivingPools;

    /**
     * @param $drivingPools DrivingPool[]
     */
    public function __construct($drivingPools) {
        $this->rideNodeLists = array();
        foreach ($drivingPools as $drivingPool) {
            $this->drivingPools[$drivingPool->getId()] = $drivingPool;
        }
        $this->totalDistance = 0;
        $this->totalEmptyRideTime = 0;
    }

    /**
     * @param RideNodeList $rideNodeList
     */
    public function addRideNodeList(RideNodeList $rideNodeList) {
        $this->totalDistance += $rideNodeList->getTotalDistance();
        $this->totalEmptyRideTime += $rideNodeList->getTotalEmptyRideTime();
        $this->totalEmptyRideDistance += $rideNodeList->getTotalEmptyRideDistance();
        $this->rideNodeLists[] = $rideNodeList;
    }

    /**
     * @param $notFeasibleNodes
     */
    public function setNotFeasibleNodes($notFeasibleNodes) {
        $this->notFeasibleNodes = $notFeasibleNodes;
    }

    /**
     * @return mixed
     */
    public function getNotFeasibleNodes() {
        return $this->notFeasibleNodes;
    }

    /**
     * returns array with key = PoolId and value = RideNodeList
     * @return RideNodeList[]
     */
    public function getRideNodeLists() {
        return $this->rideNodeLists;
    }

    /**
     * @param $position
     * @param RideNodeList $rideNodeList
     */
    public function setRideNodeListAt($position, RideNodeList $rideNodeList) {
        $this->rideNodeLists[$position] = $rideNodeList;
    }

    /**
     * @return mixed
     */
    public function getTotalDistance() {
        return $this->totalDistance;
    }

    /**
     * @return mixed
     */
    public function getTotalEmptyRideTime() {
        return $this->totalEmptyRideTime;
    }

    /**
     * @param int $totalDistance
     */
    public function setTotalDistance($totalDistance) {
        $this->totalDistance = $totalDistance;
    }

    /**
     * @param int $totalEmptyRideDistance
     */
    public function setTotalEmptyRideDistance($totalEmptyRideDistance) {
        $this->totalEmptyRideDistance = $totalEmptyRideDistance;
    }

    /**
     * @param int $totalEmptyRideTime
     */
    public function setTotalEmptyRideTime($totalEmptyRideTime) {
        $this->totalEmptyRideTime = $totalEmptyRideTime;
    }

    /**
     * @return bool
     */
    public function hasNotFeasibleNodes() {
        return (count($this->notFeasibleNodes) > 0);
    }

    /**
     * @return int
     */
    public function getTotalEmptyRideDistance() {
        return $this->totalEmptyRideDistance;
    }

    /**
     * @return DrivingPool[] with drivingPoolID => drivingPool
     */
    public function getDrivingPools() {
        return $this->drivingPools;
    }

    /**
     * @param $drivingPoolId
     * @return DrivingPool
     */
    public function getDrivingPoolFromId($drivingPoolId) {
        return $this->drivingPools[$drivingPoolId];
    }

    /**
     * @return int
     */
    public function getAmountOfUsedVehicles() {
        return count($this->rideNodeLists);
    }

    public function removeEmptyRideNodeLists() {
        foreach ($this->rideNodeLists as $key => $list) {
            if ($list->isEmpty()) {
                unset($this->rideNodeLists[$key]);
            }
        }
    }
}