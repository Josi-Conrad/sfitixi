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
     * @var
     */
    protected $notFeasibleNodes;

    /**
     * Array of RideNodeLists with ID = drivingPoolID
     * @var RideNodeList[]
     */
    protected $rideNodeLists;

    public function __construct() {
        $this->rideNodeLists = array();

        $this->totalDistance = 0;
        $this->totalEmptyRideTime = 0;
    }

    /**
     * @param DrivingPool $drivingPool
     * @return RideNodeList
     */
    public function getRideNodeListFromPool(DrivingPool $drivingPool) {
        return $this->rideNodeLists[$drivingPool->getId()];
    }

    /**
     * @param $drivingPoolId
     * @return RideNodeList
     */
    public function getRideNodeListFromPoolId($drivingPoolId) {
        return $this->rideNodeLists[$drivingPoolId];
    }

    /**
     * @param DrivingPool $drivingPool
     * @param RideNodeList $rideNodeList
     */
    public function addRideNodeListAtPool(DrivingPool $drivingPool, RideNodeList $rideNodeList) {
        $this->totalDistance += $rideNodeList->getTotalDistance();
        $this->totalEmptyRideTime += $rideNodeList->getTotalEmptyRideTime();
        $this->totalEmptyRideDistance += $rideNodeList->getTotalEmptyRideDistance();
        $this->rideNodeLists[$drivingPool->getId()] = $rideNodeList;
    }

    /**
     * @param RideNodeList $rideNodeList
     */
    public function addRideNodeList(RideNodeList $rideNodeList) {
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
     * @return RideNodeList[]
     */
    public function getRideNodeLists() {
        return $this->rideNodeLists;
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

}