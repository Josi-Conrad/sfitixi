<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.05.14
 * Time: 18:16
 */

namespace Tixi\App\AppBundle\Disposition\RideStrategies;


use Tixi\App\AppBundle\Disposition\RideConfiguration;

interface RideStrategy {
    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes);

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @param $factor
     * @return RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $drivingPools, $emptyRideNodes, $factor);
} 