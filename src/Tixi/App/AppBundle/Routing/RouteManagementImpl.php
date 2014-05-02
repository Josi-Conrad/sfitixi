<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 12:58
 */

namespace Tixi\App\AppBundle\Routing;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Routing\RouteManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\RouteRepository;

class RouteManagementImpl extends ContainerAware implements RouteManagement {
    /**
     * @param Address $from
     * @param Address $to
     * @return mixed
     */
    public function getRouteFromAddresses(Address $from, Address $to) {
        /**@var $routeRepo RouteRepository*/
        $routeRepo = $this->container->get('route_repository');
        /**@var $routingMachine \Tixi\App\Routing\RoutingMachine */
        $routingMachine = $this->container->get('tixi_app.routingmachine');
        try{
            $routingInformation = $routingMachine->getRoutingInformationFromCoordinates(
                $from->getLat(), $from->getLng(), $to->getLat(), $to->getLng());
            $route = Route::registerRoute($from, $to,
                $routingInformation->getTotalTime(), $routingInformation->getTotalDistance());
            return $route;

        }catch (Exception $e){
            return null;
        }
    }

    public function createRouteFromDrivingOrder() {
        // TODO: Implement createRouteFromDrivingOrder() method.
    }

}