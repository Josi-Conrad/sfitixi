<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:05
 */

namespace Tixi\App\Ride;


use Tixi\CoreDomain\Dispo\Shift;

/**
 * responsible for optimization processes, like rideOptimization , or feasibility check methods
 * Interface RideManagement
 * @package Tixi\App\Ride
 */
interface RideManagement {
    /**
     * @param \DateTime $dayTime
     * @param $direction
     * @param $duration
     * @param $additionalTime
     * @return bool
     */
    public function checkFeasibility(\DateTime $dayTime, $direction, $duration, $additionalTime = 0);

    /**
     * @param \DateTime $fromDateTime
     * @param \DateTime $toDate
     * @param $weekday
     * @param $direction
     * @param $duration
     * @param int $additionalTime
     * @return bool
     */
    public function checkRepeatedFeasibility(\DateTime $fromDateTime, \DateTime $toDate, $weekday, $direction, $duration, $additionalTime = 0);

    /**
     * runs routing algorithm to set optimized missions and orders for a shift
     * @param Shift $shift
     * @return bool
     */
    public function buildOptimizedPlanForShift(Shift $shift);
} 