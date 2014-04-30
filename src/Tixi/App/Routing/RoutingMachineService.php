<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:16
 */

namespace Tixi\App\Routing;


interface RoutingMachineService {

    /**
     * @param $latFrom
     * @param $lngFrom
     * @param $latTo
     * @param $lngTo
     * @return RoutingInformation
     */
    public function getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo);

} 