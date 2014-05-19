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

//    public $passengerId;
//    public $passengerFirstName;
//    public $passengerLastName;
//    public $passengerAddressId;
//
//    public $pickupDate;
//    public $pickupTime;
//    public $memo;
//    public $companion;
//
//    public $addressFromId;
//    public $addressToId;
//
//    public $routeId;
//
//    public $zoneType;
//
//    public $withDrivingOrderBack;
} 