<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:03
 */

namespace Tixi\App\Disposition;


use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingMonth;

/**
 * Interface WorkingMonthManagement
 * @package Tixi\App\Disposition
 */
interface WorkingMonthManagement {
    /**
     * @param \Tixi\CoreDomain\Dispo\WorkingMonth $workingMonth
     * @return mixed
     */
    public function assignAvailableDriversToDrivingPools(WorkingMonth $workingMonth);

    /**
     * @param WorkingMonth $workingMonth
     * @return DrivingPool[]
     */
    public function getAllUnassignedDrivingPoolsForMonth(WorkingMonth $workingMonth);
}