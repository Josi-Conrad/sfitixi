<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 09:57
 */

namespace Tixi\App\Disposition;

/**
 * Holds several values for disposition calculations
 * Class DispositionVariables
 * @package Tixi\App\Disposition
 */
class DispositionVariables {
    /**
     * Minutes needed for boarding a Passenger
     */
    const BOARDING_TIME = 3;
    /**
     * Minutes needed for deboarding a Passenger
     */
    const DEBOARDING_TIME = 3;
    /**
     * Arrive several minutes before the Pickup Order begins
     */
    const ARRIVAL_BEFORE_PICKUP = 5;


}