<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:44
 */

namespace Tixi\App\AppBundle\Ride;


use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;

/**
 * Class RideNodeList
 * @package Tixi\App\AppBundle\Ride
 */
class RideNodeList {
    /**
     * @var int
     */
    protected $drivingPoolId;
    /**
     * time above all included emptyRideNodes
     * @var int
     */
    protected $totalEmptyRideTime;
    /**
     * distance above all inserted nodes
     * @var int
     */
    protected $totalEmptyRideDistance;
    /**
     * distance above all inserted nodes
     * @var int
     */
    protected $totalDistance;
    /**
     * max amount of passengers on this ride, so we can fit a vehicle for this nodeList
     * @var int
     */
    protected $maxPassengersOnRide;
    /**
     * max amount of wheelChairs on this ride, so we can fit a vehicle for this nodeList
     * @var int
     */
    protected $maxWheelChairsOnRide;
    /**
     * all passenger contradicting vehicleCategories on this ride
     * @var $contradictingVehicleCategories VehicleCategory[]
     */
    protected $contradictingVehicleCategories;

    /**
     * list entries
     * @var int
     */
    protected $counter;
    /**@var $rideNodes RideNode[] */
    protected $rideNodes;
    /**@var $firstNode RideNode Reference */
    protected $firstNode;
    /**@var $lastNode RideNode Reference */
    protected $lastNode;

    public function __construct() {
        $this->rideNodes = array();
        $this->counter = 0;
        $this->totalDistance = 0;
        $this->totalEmptyRideTime = 0;
        $this->totalEmptyRideDistance = 0;
        $this->maxPassengersOnRide = 0;
        $this->maxWheelChairsOnRide = 0;
        $this->contradictingVehicleCategories = array();
    }

    /**
     * adds Passenger RideNodes or information from emptyRideNode
     * @param RideNode $rideNode
     */
    public function addRideNode(RideNode $rideNode) {
        if ($rideNode->type == RideNode::RIDE_PASSENGER) {
            if ($this->isEmpty()) {
                $this->setFirstNode($rideNode);
            }
            if ($this->lastNode) {
                $this->lastNode->setNextNode($rideNode);
                $rideNode->setPreviousNode($this->lastNode);
            }
            $this->setLastNode($rideNode);
            $this->rideNodes[] = $rideNode;
            $this->updateRideNodeListInformation($rideNode);
            $this->counter++;
        }
        if ($rideNode->type == RideNode::RIDE_EMPTY) {
            $this->totalEmptyRideTime += $rideNode->duration;
            $this->totalEmptyRideDistance += $rideNode->distance;
            $this->totalDistance += $rideNode->distance;
        }
    }

    /**
     * @param RideNode $rideNode
     * @param RideNode $leftNode
     */
    public function addRideNodeAfterRideNode(RideNode $rideNode, RideNode $leftNode) {
        if ($this->lastNode === $leftNode) {
            $this->setLastNode($rideNode);
        } else {
            $rightNode = $leftNode->nextNode;
            $rideNode->setNextNode($rightNode);
        }
        $leftNode->setNextNode($rideNode);
        $rideNode->setPreviousNode($leftNode);

        $this->rideNodes[] = $rideNode;
        $this->updateRideNodeListInformation($rideNode);
        $this->counter++;
    }

    /**
     * @param RideNode $rideNode
     * @param RideNode $rightNode
     */
    public function addRideNodeBeforeRideNode(RideNode $rideNode, RideNode $rightNode) {
        if ($this->firstNode === $rightNode) {
            $this->setFirstNode($rideNode);
        } else {
            $leftNode = $rightNode->previousNode;
            $rideNode->setPreviousNode($leftNode);
        }
        $rightNode->setPreviousNode($rideNode);
        $rideNode->setNextNode($rightNode);

        $this->rideNodes[] = $rideNode;
        $this->updateRideNodeListInformation($rideNode);
        $this->counter++;
    }

    /**
     * BE AWARE that changing (not only insert) the NodeList requires a full information update
     * @param $position
     * @param RideNode $rideNode
     */
    public function switchRideNodeAtPosition($position, RideNode $rideNode) {
        $switchNode = $this->rideNodes[$position];

        if ($this->lastNode === $switchNode) {
            $this->setLastNode($rideNode);
            $prev = $switchNode->previousNode;
            $rideNode->setPreviousNode($prev);
            $prev->setNextNode($rideNode);
        }
        else if($this->firstNode === $switchNode){
            $this->setFirstNode($rideNode);
            $next = $switchNode->nextNode;
            $rideNode->setNextNode($next);
            $next->setPreviousNode($rideNode);
        } else {
            $next = $switchNode->nextNode;
            $rideNode->setNextNode($next);
            $next->setPreviousNode($rideNode);
            $prev = $switchNode->previousNode;
            $rideNode->setPreviousNode($prev);
            $prev->setNextNode($rideNode);
        }

        $this->rideNodes[$position] = $rideNode;
        $this->counter++;
    }

    /**
     * @param RideNode $rideNode
     */
    private function updateRideNodeListInformation(RideNode $rideNode) {
        $this->totalDistance += $rideNode->distance;
        if ($rideNode->passengers > $this->maxPassengersOnRide) {
            $this->maxPassengersOnRide = $rideNode->passengers;
        }
        if ($rideNode->wheelChairs > $this->maxWheelChairsOnRide) {
            $this->maxWheelChairsOnRide = $rideNode->wheelChairs;
        }
        if (count($rideNode->contradictingVehicleCategories) > 0) {
            foreach ($rideNode->contradictingVehicleCategories as $key => $cat) {
                $this->contradictingVehicleCategories[$key] = $cat;
            }
        }
    }

    private function recreateRideNodeListInformation($emptyRides){
        foreach($this->rideNodes as $nodes){

        }
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return (count($this->rideNodes) < 1);
    }

    /**
     * @return RideNode
     */
    public function getActualRideNode() {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->lastNode;
    }

    /**
     * @return RideNode[]
     */
    public function getRideNodes() {
        return $this->rideNodes;
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
     * @return int
     */
    public function getTotalEmptyRideDistance() {
        return $this->totalEmptyRideDistance;
    }

    /**
     * @return int
     */
    public function getCounter() {
        return $this->counter;
    }

    /**
     * @return mixed
     */
    public function getFirstNode() {
        return $this->firstNode;
    }

    /**
     * @param \Tixi\App\AppBundle\Ride\RideNode $firstNode
     */
    public function setFirstNode(&$firstNode) {
        $this->firstNode = $firstNode;
    }

    /**
     * @param \Tixi\App\AppBundle\Ride\RideNode $lastNode
     */
    public function setLastNode(&$lastNode) {
        $this->lastNode = $lastNode;
    }

    /**
     * @return mixed
     */
    public function getLastNode() {
        return $this->lastNode;
    }

    /**
     * @return int
     */
    public function getMaxPassengersOnRide() {
        return $this->maxPassengersOnRide;
    }

    /**
     * @return int
     */
    public function getMaxWheelChairsOnRide() {
        return $this->maxWheelChairsOnRide;
    }

    /**
     * @param int $drivingPoolId
     */
    public function setDrivingPoolId($drivingPoolId) {
        $this->drivingPoolId = $drivingPoolId;
    }

    /**
     * @return int
     */
    public function getDrivingPoolId() {
        return $this->drivingPoolId;
    }

    /**
     * @param VehicleCategory $category
     * @return bool
     */
    public function vehicleCategoryIsContradicting(VehicleCategory $category) {
        foreach ($this->contradictingVehicleCategories as $contradict) {
            if ($category->getId() == $contradict->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Vehicle $vehicle
     * @return bool
     */
    public function vehicleIsCompatibleWithThisList(Vehicle $vehicle) {
        return ($vehicle->isCompatibleWithPassengerAndWheelChairAmount($this->getMaxPassengersOnRide(), $this->getMaxWheelChairsOnRide())
            && !$this->vehicleCategoryIsContradicting($vehicle->getCategory()));
    }
}