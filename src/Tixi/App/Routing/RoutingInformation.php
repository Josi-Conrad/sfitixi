<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:20
 */

namespace Tixi\App\Routing;


abstract class RoutingInformation {
    public abstract function getStatus();

    public abstract function getTotalTime();

    public abstract function getTotalDistance();
} 