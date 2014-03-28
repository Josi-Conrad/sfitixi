<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;


class DrivingMission {

    protected $id;
    /**
     * @var DrivingPool
     */
    protected $drivingPool;
    /**
     * @var array
     */
    protected $drivingOrders;

    /**
     * returns earliest pickUpType of orders
     */
    protected function getAnchorTime() {

    }

    /**
     * returns mission duration in tbd(seconds|minutes)
     */
    protected function getDuration() {

    }
} 