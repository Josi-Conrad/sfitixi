<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:44
 */

namespace Tixi\App\AppBundle\Ride;


use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;

/**
 * Class RideNodeList
 * @package Tixi\App\AppBundle\Ride
 */
class RideNodeList {
    /**
     * assigned pool for this ride
     * @var DrivingPool
     */
    protected $drivingPool;
    /**
     * assigned vehicle for this ride
     * @var Vehicle
     */
    protected $vehicle;
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
        $prev = $switchNode->previousNode;
        $next = $switchNode->nextNode;
        if ($prev) {
            $rideNode->setPreviousNode($prev);
            $prev->setNextNode($rideNode);
        } else {
            $rideNode->removePreviousNode();
        }
        if ($next) {
            $rideNode->setNextNode($next);
            $next->setPreviousNode($rideNode);
        } else {
            $rideNode->removeNextNode();
        }
        if ($this->lastNode === $switchNode) {
            $this->setLastNode($rideNode);
        }
        if ($this->firstNode === $switchNode) {
            $this->setFirstNode($rideNode);
        }
        $this->rideNodes[$position] = $rideNode;
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

    private function recreateRideNodeListInformation($emptyRides) {
        foreach ($this->rideNodes as $nodes) {

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
     * @param DrivingPool $drivingPool
     */
    public function assignDrivingPool(DrivingPool $drivingPool) {
        $this->drivingPool = $drivingPool;
    }

    /**
     * @return DrivingPool
     */
    public function getDrivingPool() {
        return $this->drivingPool;
    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignVehicle(Vehicle $vehicle) {
        $this->vehicle = $vehicle;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle() {
        return $this->vehicle;
    }


    /**
     * @param int $maxPassengersOnRide
     */
    public function setMaxPassengersOnRide($maxPassengersOnRide) {
        $this->maxPassengersOnRide = $maxPassengersOnRide;
    }

    /**
     * @param int $maxWheelChairsOnRide
     */
    public function setMaxWheelChairsOnRide($maxWheelChairsOnRide) {
        $this->maxWheelChairsOnRide = $maxWheelChairsOnRide;
    }

    /**
     * @param int $totalEmptyRideTime
     */
    public function setTotalEmptyRideTime($totalEmptyRideTime) {
        $this->totalEmptyRideTime = $totalEmptyRideTime;
    }

    /**
     * @param int $totalEmptyRideDistance
     */
    public function setTotalEmptyRideDistance($totalEmptyRideDistance) {
        $this->totalEmptyRideDistance = $totalEmptyRideDistance;
    }

    /**
     * @param int $totalDistance
     */
    public function setTotalDistance($totalDistance) {
        $this->totalDistance = $totalDistance;
    }

    /**
     * @param \Tixi\CoreDomain\VehicleCategory[] $contradictingVehicleCategories
     */
    public function setContradictingVehicleCategories($contradictingVehicleCategories) {
        $this->contradictingVehicleCategories = $contradictingVehicleCategories;
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

    /**
     * @param Driver $driver
     * @return bool
     */
    public function driverIsCompatibleWithThisList(Driver $driver) {
        $wh = $driver->getWheelChairAttendance();
        if ($this->getMaxWheelChairsOnRide() > 1 && $wh == false) {
            return false;
        }
        foreach ($driver->getContradictVehicleCategories() as $contradictCategory) {
            if ($this->vehicle->getCategory()->getId() == $contradictCategory->getId()) {
                return false;
            }
        }
        return true;
    }
}