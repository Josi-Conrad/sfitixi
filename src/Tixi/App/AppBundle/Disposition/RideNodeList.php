<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:44
 */

namespace Tixi\App\AppBundle\Disposition;


class RideNodeList {

    protected $counter;

    protected $totalEmptyRideTime;
    protected $totalDistance;

    /**
     * @var RideNode[]
     */
    protected $rideNodes;

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
            $this->counter++;
            $this->rideNodes[] = $rideNode;
            $this->totalDistance += $rideNode->distance;
        }
        if ($rideNode->type == RideNode::RIDE_EMPTY) {
            $this->totalEmptyRideTime += $rideNode->duration;
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
}