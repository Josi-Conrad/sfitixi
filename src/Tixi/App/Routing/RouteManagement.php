<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:58
 */

namespace Tixi\App\Routing;


use Tixi\CoreDomain\Address;

interface RouteManagement {

    /**
     * @param Address $from
     * @param Address $to
     * @return mixed
     */
    public function getRouteFromAddresses(Address $from, Address $to);

    public function createRouteFromDrivingOrder();

} 