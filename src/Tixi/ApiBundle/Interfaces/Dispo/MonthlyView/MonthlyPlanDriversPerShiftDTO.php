<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 22:26
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;


class MonthlyPlanDriversPerShiftDTO {

    public $shiftDisplayName;
    public $newDrivers = array();
    public $driversWithAssertion = array();
} 