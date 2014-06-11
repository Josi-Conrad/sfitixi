<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:58
 */

namespace Tixi\App\Routing;


use Tixi\CoreDomain\Address;

/**
 * Services for Route Management. If only RoutingInformation is needed, use routingMachine
 *
 * Interface RouteManagement
 * @package Tixi\App\Routing
 */
interface RouteManagement {

    /**
     * checks if existing route in database is available or query new routing informations from an routing machine
     * and return a Route object
     * @param Address $from
     * @param Address $to
     * @return mixed
     */
    public function getRouteFromAddresses(Address $from, Address $to);

    /**
     * fills routing informations (duration and distance) for multiple rideNode objects
     * @param $rideNodes
     * @return mixed
     */
    public function fillRoutesForMultipleRideNodes($rideNodes);

}
