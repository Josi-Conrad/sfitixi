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
 * Class RoutingMachineController
 * Get json routing information for coordinates, for example:
 * /service/routing?latFrom=47.498796&lngFrom=7.760499&latTo=47.049796&lngTo=8.548057
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class RoutingMachineController extends Controller {
    /**
     * @Route("/routing",name="tixiapp_service_routing")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRoutingInformationAction(Request $request) {
        /**@var $routingMachine RoutingMachine */
        $routingMachine = $this->container->get('tixi_app.routingmachine');

        $latFrom = $request->get('latFrom');
        $lngFrom = $request->get('lngFrom');

        $latTo = $request->get('latTo');
        $lngTo = $request->get('lngTo');

        try {
            $routingInformation = $routingMachine->getRoutingInformationFromCoordinates($latFrom, $lngFrom, $latTo, $lngTo);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $response = new JsonResponse();
        $response->setData(array(
            'routeDuration' => $routingInformation->getTotalTime(),
            'routeDistance' => $routingInformation->getTotalDistance()
        ));
        return $response;
    }
} 