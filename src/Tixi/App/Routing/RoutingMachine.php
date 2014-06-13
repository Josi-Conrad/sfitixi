<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:16
 */

namespace Tixi\App\Routing;


use Tixi\App\AppBundle\Routing\RoutingClassOSRM;
use Tixi\App\AppBundle\Routing\RoutingCoordinate;
use Tixi\App\AppBundle\Routing\RoutingMachineException;
use Tixi\CoreDomain\Dispo\Route;

/**
 * Service for RoutingMachine Requests
 * Interface RoutingMachine
 * @package Tixi\App\Routing
 */
interface RoutingMachine {

    /**
     * gets nearest routable waypoint coordinates from latitude and longitude (float)
     * of an address
     * @param $lat
     * @param $lng
     * @return RoutingCoordinate
     * @throws RoutingMachineException
     */
    public function getNearestPointsFromCoordinates($lat, $lng);

    /**
     * Gets RoutingInformation from (not routable) Coordinates.
     * This methods uses additional requests to get the nearest routable waypoint coordinates
     * @param $latFrom
     * @param $lngFrom
     * @param $latTo
     * @param $lngTo
     * @return RoutingInformation
     * @throws RoutingMachineException
     */
    public function getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo);

    /**
     * Gets RoutingInformation from (routable) RoutingCoordinates.
     * This methods requires already routable waypoint coordinates to calculate routing informations
     * @param \Tixi\App\AppBundle\Routing\RoutingCoordinate $cordFrom
     * @param \Tixi\App\AppBundle\Routing\RoutingCoordinate $cordTo
     * @return mixed
     * @throws RoutingMachineException
     */
    public function getRoutingInformationFromRoutingCoordinates(RoutingCoordinate $cordFrom, RoutingCoordinate $cordTo);

    /**
     * This functions fills an existing array of Route objects with distance and duration
     * For multiple object curl_multi asynchronous requests are generated
     * -> much higher performance against single curl requests
     *
     * @param Route[] $routes with hashKeys from coordinates
     * @return Route[]
     * @throws RoutingMachineException
     * @throws \Exception
     */
    public function fillRoutingInformationForMultipleRoutes($routes);
} 