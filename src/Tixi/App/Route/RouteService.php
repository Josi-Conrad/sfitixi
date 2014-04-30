<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:58
 */

namespace Tixi\App\Route;


use Tixi\CoreDomain\Address;

interface RouteService {

    /**
     * @param Address $from
     * @param Address $to
     * @return mixed
     */
    public function getRoute(Address $from, Address $to);

    public function createRouteFromDrivingOrder();

} 