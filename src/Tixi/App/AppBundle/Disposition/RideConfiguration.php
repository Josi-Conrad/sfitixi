<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:26
 */

namespace Tixi\App\AppBundle\Disposition;


use Tixi\CoreDomain\Dispo\DrivingPool;

class RideConfiguration {
    protected $totalEmptyRideTime;
    protected $totalDistance;
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

    public function hasNotFeasibleNodes(){
        return isset($this->notFeasibleNodes);
    }
}