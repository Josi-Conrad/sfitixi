<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:20
 */

namespace Tixi\App\Routing;


abstract class RoutingInformation {

    protected $totalTime;
    protected $totalDistance;

    /**
     * get total route trip time in seconds
     * @return mixed
     */
    public abstract function getTotalTime();

    /**
     * get total route distance in meters
     * @return mixed
     */
    public abstract function getTotalDistance();
} 