<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:05
 */

namespace Tixi\App\Ride;


use Tixi\CoreDomain\Dispo\Shift;

interface RideManagement {
    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @param $direction
     * @param $duration
     * @param $additionalTime
     * @return bool
     */
    public function checkFeasibility(\DateTime $day, \DateTime $time, $direction, $duration, $additionalTime);

    /**
     * runs routing algorithm to set optimized missions and orders for a shift
     * @param Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift);
} 