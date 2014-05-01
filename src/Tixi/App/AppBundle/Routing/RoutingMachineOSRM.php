<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 10:18
 */

namespace Tixi\App\AppBundle\Routing;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Routing\RoutingInformation;
use Tixi\App\Routing\RoutingMachine;

class RoutingMachineOSRM extends ContainerAware implements RoutingMachine {
    const SUCCESS = 0;
    const NO_ROUTE = 207;

    protected $osrm_server;

    /**
     * @param $latFrom
     * @param $lngFrom
     * @param $latTo
     * @param $lngTo
     * @throws \Exception
     * @throws RoutingMachineException
     * @return RoutingInformation
     */
    public function getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo) {
        $this->prepareServerUrl();

        //gets nearest Points by OSRM before trying to calculate a route
        $cordFrom = $this->getNearestPoint(new RoutingCoordinate($latFrom, $lngFrom));
        $cordTo = $this->getNearestPoint(new RoutingCoordinate($latTo, $lngTo));

        return $this->getRouteInformation($cordFrom, $cordTo);

    }

    /**
     * Gives nearest point of any street segment to given coordinations
     * https://github.com/DennisOSRM/Project-OSRM/wiki/Server-api
     * for example: http://seiout.ch:8080/nearest?loc=47.096531,8.463097
     * @param RoutingCoordinate $coordinate
     * @return RoutingCoordinate
     * @throws RoutingMachineException
     */
    private function getNearestPoint(RoutingCoordinate $coordinate) {
        $requestUrl = $this->osrm_server . 'nearest?' . $coordinate;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $requestUrl,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        if ($response === false) {
            throw new RoutingMachineException("OSRM connection failed");
        }
        $json = json_decode($response);
        if ($json === null) {
            throw new RoutingMachineException("OSRM answer parsing failed");
        }
        if ($json->status === self::SUCCESS) {
            $lat = $json->mapped_coordinate[0];
            $lng = $json->mapped_coordinate[1];
            $nearestCoordinate = new RoutingCoordinate($lat, $lng);
            return $nearestCoordinate;
        } else {
            throw new RoutingMachineException("OSRM status error", $json->{'status'});
        }
    }

    /**
     * Gives route between 2 points. Coordinates should be "nearest" coordinates to street segment from OSRM
     * https://github.com/DennisOSRM/Project-OSRM/wiki/Server-api
     * for example: http://seiout.ch:8080/viaroute?loc=47.498796,7.760499&loc=47.049796,8.548057
     * @param RoutingCoordinate $coordinateFrom
     * @param RoutingCoordinate $coordinateTo
     * @throws RoutingMachineException
     * @return null|RoutingInformationOSRM
     */
    private function getRouteInformation(RoutingCoordinate $coordinateFrom, RoutingCoordinate $coordinateTo) {
        $requestUrl = $this->osrm_server . 'viaroute?' . $coordinateFrom . '&' . $coordinateTo;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $requestUrl,
        ));

        $resp = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($resp);

        if ($json->status === self::SUCCESS) {
            $route = new RoutingInformationOSRM();
            $route->setTotalTime($json->route_summary->total_time);
            $route->setTotalDistance($json->route_summary->total_distance);
            $route->setChecksum($json->hint_data->checksum);

            return $route;
        } else {
            throw new RoutingMachineException("OSRM status error", $json->{'status'});
        }
    }

    /**
     * Prepares OSRM server URL from parameter, adds / if last character is not an /
     */
    private function prepareServerUrl() {
        $this->osrm_server = $this->container->getParameter('tixi_parameter_osrm_server');
        if (substr($this->osrm_server, -1) !== '/') {
            $this->osrm_server = $this->osrm_server . '/';
        }
    }
}