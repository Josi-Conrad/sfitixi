<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:16
 */

namespace Tixi\App\Routing;


interface RoutingMachine {

    /**
     * @param $latFrom
     * @param $lngFrom
     * @param $latTo
     * @param $lngTo
     * @return RoutingInformation
     */
    public function getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo);

    /**
     * This functions fills an existing array of Route objects with distance and duration
     * BEWARE: No return objects! It writes in to the existing array by reference!
     * For multiple object curl_multi asynchronous requests are generated
     * -> much higher performance against single curl requests
     *
     * @param array $routes by reference
     * @throws RoutingMachineException
     * @throws \Exception
     */
    public function fillRoutingInformationsForMultipleRoutes(array &$routes);
} 