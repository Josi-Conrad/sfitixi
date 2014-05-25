<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:44
 */

namespace Tixi\App\AppBundle\Disposition;


class RideNodeList {
    protected $totalPassengers;
    protected $totalMissions;
    protected $totalWaitingTime;
    protected $totalDistance;

    protected $rideNodes;

    public function __construct(){
        $this->rideNodes = array();
    }

    public function addRideNode(RideNode $rideNode){
        $this->rideNodes[] = $rideNode;
    }
} 