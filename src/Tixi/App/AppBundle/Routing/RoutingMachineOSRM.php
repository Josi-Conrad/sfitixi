<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 10:18
 */

namespace Tixi\App\AppBundle\Routing;


use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Routing\RoutingInformation;
use Tixi\App\Routing\RoutingMachine;
use Tixi\CoreDomain\Dispo\Route;

class RoutingMachineOSRM extends ContainerAware implements RoutingMachine {
    /**
     * defines status codes from OSRM API
     */
    const SUCCESS = 0;
    const NO_ROUTE = 207;

    /**
     * defines running curl requests at same time
     */
    const BLOCK_SIZE = 100;

    /**
     * defines OSRM API usage parameters
     */
    const NEAREST = 'nearest?';
    const VIAROUTE = 'viaroute?';

    /**
     * @var $osrm_server parameter from osrm_server (tixi_parameter_osrm_server)
     */
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
        if (!$this->checkConnectivity()) {
            throw new RoutingMachineException('OSRM connection failed');
        }
        //gets nearest Points by OSRM before trying to calculate a route
        $cordFrom = $this->getNearestPoint(new RoutingCoordinate($latFrom, $lngFrom));
        $cordTo = $this->getNearestPoint(new RoutingCoordinate($latTo, $lngTo));

        return $this->getRouteInformation($cordFrom, $cordTo);
    }

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
    public function fillRoutingInformationsForMultipleRoutes(array &$routes) {
        $this->prepareServerUrl();
        if (!$this->checkConnectivity()) {
            throw new RoutingMachineException('OSRM connection failed');
        }
        $routings = array();
        foreach ($routes as &$route) {
            if (!($route instanceof Route)) {
                throw new \Exception('Entries in $routes must be Instance of Route');
            }
            array_push($routings, new RoutingClassOSRM($route));
        }
        echo 'Creating RoutingClasses done' . "\n";
        $this->fillNearestPoints($routings);
        echo 'Fill NearestPoints done' . "\n";
        $this->setRoutingInformations($routings);
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
        $requestUrl = $this->osrm_server . self::NEAREST . $coordinate;

        $curl = $this->createCurlRequest($requestUrl);

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
        $requestUrl = $this->osrm_server . self::VIAROUTE . $coordinateFrom . '&' . $coordinateTo;

        $curl = $this->createCurlRequest($requestUrl);

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
     * @param array $routings
     */
    private function fillNearestPoints(array &$routings) {
        $indicator = count($routings);

        $curlHandlesFrom = array();
        $curlHandlesTo = array();

        //generate curl requests
        for ($i = 0; $i < $indicator; $i++) {
            $route = $routings[$i]->getRoute();

            //set all from start coordinates
            $coordinateFrom = new RoutingCoordinate(
                $route->getStartAddress()->getLat(),
                $route->getStartAddress()->getLng());
            $curlRequestFrom = $this->createCurlRequest($this->osrm_server . self::NEAREST . $coordinateFrom);
            array_push($curlHandlesFrom, $curlRequestFrom);

            //set all target coordinates
            $coordinateTo = new RoutingCoordinate(
                $route->getTargetAddress()->getLat(),
                $route->getTargetAddress()->getLng());

            $curlRequestTo = $this->createCurlRequest($this->osrm_server . self::NEAREST . $coordinateTo);
            array_push($curlHandlesTo, $curlRequestTo);
        }

        $responsesFrom = $this->runMultiCurlsBlockWise($indicator, $curlHandlesFrom);
        $responsesTo = $this->runMultiCurlsBlockWise($indicator, $curlHandlesTo);

        for ($i = 0; $i < $indicator; $i++) {
            $json = json_decode($responsesFrom[$i]);
            $lat = $json->mapped_coordinate[0];
            $lng = $json->mapped_coordinate[1];
            $routings[$i]->setNearestFromLat($lat);
            $routings[$i]->setNearestFromLng($lng);
        }

        for ($i = 0; $i < $indicator; $i++) {
            $json = json_decode($responsesTo[$i]);
            $lat = $json->mapped_coordinate[0];
            $lng = $json->mapped_coordinate[1];
            $routings[$i]->setNearestToLat($lat);
            $routings[$i]->setNearestToLng($lng);
        }
    }

    /**
     * @param array $routings
     */
    private function setRoutingInformations(array &$routings) {
        $indicator = count($routings);
        $curlHandles = array();

        //generate curl requests
        for ($i = 0; $i < $indicator; $i++) {
            $route = $routings[$i]->getRoute();
            //set all from start coordinates
            $coordinateFrom = new RoutingCoordinate(
                $route->getStartAddress()->getLat(),
                $route->getStartAddress()->getLng());

            //set all target coordinates
            $coordinateTo = new RoutingCoordinate(
                $route->getTargetAddress()->getLat(),
                $route->getTargetAddress()->getLng());

            $curlRequest = $this->createCurlRequest($this->osrm_server . self::VIAROUTE . $coordinateFrom . '&' . $coordinateTo);
            $curlHandles[] = $curlRequest;
        }

        $responses = $this->runMultiCurlsBlockWise($indicator, $curlHandles);

        for ($i = 0; $i < $indicator; $i++) {
            $route = $routings[$i]->getRoute();
            $json = json_decode($responses[$i]);

            $totalTime = $json->route_summary->total_time;
            $totalDistance = $json->route_summary->total_distance;

            $route->setDuration($totalTime);
            $route->setDistance($totalDistance);
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

    /**
     * @param $requestUrl
     * @return resource
     */
    private function createCurlRequest($requestUrl) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, 1);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        return $curl;
    }

    /**
     * @return bool
     */
    private function checkConnectivity() {
        $curl = curl_init($this->osrm_server);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        if ($response) return true;
        return false;
    }

    /**
     * @param $indicator
     * @param $curlHandles
     * @throws RoutingMachineException
     * @return array
     */
    private function runMultiCurlsBlockWise($indicator, $curlHandles) {
        //here comes the tricky part, we run curl_multi only on a BLOCKSIZE so we don't generate a flood
        $curlMultiHandle = curl_multi_init();

        $responses = array();

        $blockCount = 0; // count where we are in the list so we can break up the runs into smaller blocks
        $blockResults = array(); // to accumulate the curl_handles for each group we'll run simultaneously

        for ($i = 0; $i < $indicator; $i++) {
            $blockCount++;
            $curlHandle = $curlHandles[$i];
            // add the handle to the curl_multi_handle and to our tracking "block"
            curl_multi_add_handle($curlMultiHandle, $curlHandle);
            array_push($blockResults, $curlHandle);

            // -- check to see if we've got a "full block" to run or if we're at the end of out list of handles
            if (($blockCount % self::BLOCK_SIZE === 0) or ($blockCount === count($curlHandles))) {
                $running = NULL;
                do {
                    // track the previous loop's number of handles still running so we can tell if it changes
                    $runningBefore = $running;
                    // run the block or check on the running block and get the number of sites still running in $running
                    curl_multi_exec($curlMultiHandle, $running);
                    // if the number of sites still running changed, print out a message with the number of sites that are still running.
                    if ($running != $runningBefore) {
                        //waiting for running sites to finish
                    }
                } while ($running > 0);

                // -- once the number still running is 0, curl_multi_ is done, so check the results
                foreach ($blockResults as $result) {
                    // HTTP response code
                    $code = curl_getinfo($result, CURLINFO_HTTP_CODE);
                    // cURL error number
                    $curlErrno = curl_errno($result);
                    // cURL error message
                    $curlError = curl_error($result);
                    // output if there was an error
                    if ($curlError) {
                        throw new RoutingMachineException("OSRM cURL error: ($curlErrno) $curlError\n");
                    }

                    // fill results from our requests we get
                    $response = curl_multi_getcontent($result);
                    if ($response) {
                        array_push($responses, $response);
                    }

                    // remove the (used) handle from the curl_multi_handle
                    curl_multi_remove_handle($curlMultiHandle, $result);
                }
                // reset the block to empty, since we've run its curl_handles
                $blockResults = array();
            }
        }

        // close the curl_multi_handle once we're done
        curl_multi_close($curlMultiHandle);
        return $responses;
    }
}