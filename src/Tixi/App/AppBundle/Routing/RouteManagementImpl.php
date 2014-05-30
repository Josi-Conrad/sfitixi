<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 12:58
 */

namespace Tixi\App\AppBundle\Routing;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\App\Routing\RouteManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\RouteRepository;

class RouteManagementImpl extends ContainerAware implements RouteManagement {
    /**
     * checks if existing route in database is available or query new routing informations from an routing machine
     * and return an Route object
     * @param Address $from
     * @param Address $to
     * @return Route
     */
    public function getRouteFromAddresses(Address $from, Address $to) {
        /**@var EntityManager */
        $em = $this->container->get('entity_manager');
        /**@var $routeRepo RouteRepository */
        $routeRepo = $this->container->get('route_repository');
        /**@var $routingMachine \Tixi\App\Routing\RoutingMachine */
        $routingMachine = $this->container->get('tixi_app.routingmachine');

        // search for existing route in DB, else create a new one with querying routing machine
        $route = $routeRepo->findRouteWithAddresses($from, $to);
        if ($route !== null) {
            return $route;
        }

        // to get route from routing machine, make shure we have routable coordinates available
        $cordFrom = null;
        if (empty($from->getNearestLat()) || empty($from->getNearestLng())) {
            $cordFrom = $routingMachine->getNearestPointsFromCoordinates($from->getLat(), $from->getLng());
            $from->setNearestLat(($cordFrom->getLatitude()));
            $from->setNearestLng(($cordFrom->getLongitude()));
        } else {
            $cordFrom = new RoutingCoordinate($from->getNearestLat(), $from->getNearestLng());
        }

        $cordTo = null;
        if (empty($to->getNearestLat()) || empty($to->getNearestLng())) {
            $cordTo = $routingMachine->getNearestPointsFromCoordinates($to->getLat(), $to->getLng());
            $to->setNearestLat(($cordTo->getLatitude()));
            $to->setNearestLng(($cordTo->getLongitude()));
        } else {
            $cordTo = new RoutingCoordinate($to->getNearestLat(), $to->getNearestLng());
        }

        try {
            $routingInformation = $routingMachine->getRoutingInformationFromRoutingCoordinates($cordFrom, $cordTo);
            $route = Route::registerRoute($from, $to,
                $routingInformation->getTotalTime(), $routingInformation->getTotalDistance());
            $routeRepo->store($route);
            $em->flush();
            return $route;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Test 21.05.2014: its faster if we get all routes threw routingMachine
     * then checking first if we got a database entry...
     *
     * @param RideNode[] $rideNodes
     * @throws \Exception
     * @return RideNode[]
     */
    public function fillRoutesForMultipleRideNodes($rideNodes) {
        /**@var $routingMachine \Tixi\App\Routing\RoutingMachine */
        $routingMachine = $this->container->get('tixi_app.routingmachine');

        $routesToQuery = array();
        /** @var $rideNode \Tixi\App\AppBundle\Ride\RideNode */
        foreach ($rideNodes as $hashKey => $rideNode) {
            $routesToQuery[$hashKey] = Route::registerRoute($rideNode->startAddress, $rideNode->targetAddress);
        }
        try {
            $filledRoutings = $routingMachine->fillRoutingInformationForMultipleRoutes($routesToQuery);

            foreach ($filledRoutings as $hashKey => $route) {
                $rideNodes[$hashKey]->duration = $route->getDurationInMinutes();
                $rideNodes[$hashKey]->distance = $route->getDistanceInMeters();
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $rideNodes;
    }
}