<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:44
 */

namespace Tixi\App\AppBundle\Ride;


use Tixi\App\AppBundle\Ride\RideNode;

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
     * list entries
     * @var int
     */
    protected $counter;
    /**@var RideNode[] */
    protected $rideNodes;
    /**@var &RideNode */
    protected $firstNode;
    /**@var &RideNode */
    protected $lastNode;

    public function __construct() {
        $this->counter = 0;
        $this->rideNodes = array();

        $this->totalEmptyRideTime = 0;
        $this->totalDistance = 0;
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

}