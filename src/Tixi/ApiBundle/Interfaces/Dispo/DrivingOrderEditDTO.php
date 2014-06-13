<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 07.06.14
 * Time: 18:39
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

/**
 * Class DrivingOrderEditDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class DrivingOrderEditDTO {
    public $id;
    public $pickupDate;
    public $pickupTime;
    public $lookaheadaddressFrom;
    public $lookaheadaddressTo;
    public $zoneName;
    public $compagnion;
    public $memo;
    public $additionalTime;
    public $orderStatus;
} 