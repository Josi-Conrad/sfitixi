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
                $this->firstNode = & $rideNode;
            }
            if ($this->lastNode) {
                $this->lastNode->setNextNode($rideNode);
                $rideNode->setPreviousNode($this->lastNode);
            }
            $this->lastNode = & $rideNode;
            $this->rideNodes[] = $rideNode;
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
            $this->counter++;
        }
        if ($rideNode->type == RideNode::RIDE_EMPTY) {
            $this->totalEmptyRideTime += $rideNode->duration;
            $this->totalEmptyRideDistance += $rideNode->distance;
            $this->totalDistance += $rideNode->distance;
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
        return $this->rideNodes[$this->counter - 1];
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