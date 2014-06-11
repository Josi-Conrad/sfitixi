<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.05.14
 * Time: 18:16
 */

namespace Tixi\App\AppBundle\Ride\RideStrategies;


use Tixi\App\AppBundle\Ride\RideConfiguration;

/**
 * Interface RideStrategy
 * @package Tixi\App\AppBundle\Ride\RideStrategies
 */
interface RideStrategy {
    /**
     * @param $rideNodes
     * @param $drivingPools
     * @param $emptyRideNodes
     * @param RideConfiguration $existingConfiguration
     * @return RideConfiguration
     */
    public function buildConfiguration($rideNodes, $drivingPools, $emptyRideNodes, RideConfiguration $existingConfiguration = null);

    /**
     * @param $rideNodes
     * @param $emptyRideNodes
     * @param $drivingPools
     * @return RideConfiguration[]
     */
    public function buildConfigurations($rideNodes, $drivingPools, $emptyRideNodes);
} 