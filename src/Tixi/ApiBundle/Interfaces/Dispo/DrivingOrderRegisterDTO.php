<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:36
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

/**
 * Class DrivingOrderRegisterDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class DrivingOrderRegisterDTO {

    public $id;
    public $anchorDate;
    public $lookaheadaddressFrom;
    public $lookaheadaddressTo;
    public $zoneName;
    public $orderTime;
    public $isRepeated;
    public $compagnion;
    public $memo;
    public $additionalTime;

    //repeated part
    public $withHolidays;
    public $endDate;
    public $mondayOrderTime;
    public $tuesdayOrderTime;
    public $wednesdayOrderTime;
    public $thursdayOrderTime;
    public $fridayOrderTime;
    public $saturdayOrderTime;
    public $sundayOrderTime;

} 