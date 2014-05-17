<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:03
 */

namespace Tixi\App\Disposition;


use Tixi\CoreDomain\Dispo\DrivingOrder;

/**
 * Interface DispositionManagement
 * @package Tixi\App\Disposition
 */
interface DispositionManagement {

    /**
     * checks if a drivingOrder is possible
     * @param \Tixi\CoreDomain\Dispo\DrivingOrder $drivingOrder
     * @return mixed
     */
    public function checkFeasibility(DrivingOrder $drivingOrder);

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan();
}