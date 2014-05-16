<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:06
 */

namespace Tixi\App\AppBundle\Disposition;


use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\DrivingOrder;

class DispositionManagementImpl implements DispositionManagement{

    /**
     * checks if a drivingOrder is possible
     * @param \Tixi\CoreDomain\Dispo\DrivingOrder $drivingOrder
     * @return mixed
     */
    public function checkFeasibility(DrivingOrder $drivingOrder) {
        // TODO: Implement checkFeasibility() method.
    }

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan() {
        // TODO: Implement getOptimizedDayPlan() method.
    }
}