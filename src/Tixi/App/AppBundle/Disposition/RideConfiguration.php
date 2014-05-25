<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 25.05.14
 * Time: 20:26
 */

namespace Tixi\App\AppBundle\Disposition;


class RideConfiguration {
    protected $totalDuration;
    protected $totalDistance;

    protected $rideNodeLists;

    public function __construct() {
        $this->rideNodeLists = array();
    }

    public function addRideNodeList(RideNodeList $rideNodeList) {
        $this->rideNodeLists[] = $rideNodeList;
    }
}