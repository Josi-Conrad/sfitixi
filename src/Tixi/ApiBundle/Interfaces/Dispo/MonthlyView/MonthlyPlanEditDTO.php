<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:51
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;

/**
 * Class MonthlyPlanEditDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo\MonthlyView
 */
class MonthlyPlanEditDTO {

    public $workingMonthId;
    public $workingDayId;
    public $workingMonthDateString;
    public $workingDayWeekdayString;
    public $workingDayDateString;
    public $shifts = array();

} 