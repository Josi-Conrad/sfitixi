<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.06.14
 * Time: 13:58
 */

namespace Tixi\App\AppBundle\Interfaces;


class DrivingOrderHandleDTO {

    public $id;
    public $anchorDate;
    public $lookaheadaddressFrom;
    public $lookaheadaddressTo;
    public $zoneStatus;
    public $zoneId;
    public $zoneName;
    public $orderTime;
    public $isRepeated;
    public $compagnion;
    public $memo;

    //repeated part
    public $endDate;
    public $mondayOrderTime;
    public $tuesdayOrderTime;
    public $wednesdayOrderTime;
    public $thursdayOrderTime;
    public $fridayOrderTime;
    public $saturdayOrderTime;
    public $sundayOrderTime;
} 