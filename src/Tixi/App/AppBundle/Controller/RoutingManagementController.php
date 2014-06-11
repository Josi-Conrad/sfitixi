<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 16:11
 */

namespace Tixi\App\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\App\Routing\RoutingMachine;

/**
 * Class RoutingManagementController
 * Get json routing information for coordinates, for example:
 * /service/routing?latFrom=47.498796&lngFrom=7.760499&latTo=47.049796&lngTo=8.548057
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class RoutingManagementController extends Controller {
    /**
     * gets RoutingInformations to the outward and return routes for lat/lng
     * /service/routing?latFrom=47.498796&lngFrom=7.760499&latTo=47.049796&lngTo=8.548057
     * outwardRoute = coordinates From -> coordinates To
     * returnToue = coordinates To -> coordinates From
     * @Route("/routing",name="tixiapp_service_routing")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOutwardAndReturnRoutingInformationAction(Request $request) {
        /**@var $routingMachine RoutingMachine */
        $routingMachine = $this->container->get('tixi_app.routingmachine');

        $latFrom = $request->get('latFrom');
        $lngFrom = $request->get('lngFrom');

        $latTo = $request->get('latTo');
        $lngTo = $request->get('lngTo');

        try {
            $routingOutwardInformation = $routingMachine->getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo);
            $routingReturnInformation = $routingMachine->getRoutingInformationFromCoordinates($latTo, $lngTo, $latFrom, $lngFrom);
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1',
            ));
            return $response;
        }

        $response = new JsonResponse();
        $response->setData(array(
            'status' => '0',
            'routeOutwardDuration' => $routingOutwardInformation->getTotalTime(),
            'routeOutwardDistance' => $routingOutwardInformation->getTotalDistance(),
            'routeReturnDuration' => $routingReturnInformation->getTotalTime(),
            'routeReturnDistance' => $routingReturnInformation->getTotalDistance()
        ));
        return $response;
    }
} 