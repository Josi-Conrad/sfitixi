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
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\ZonePlan;

/**
 * Class ZonePlanController
 * Get json routing information for coordinates, for example:
 * /service/zoneplan?city=Aesch&plz=4147
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class ZonePlanController extends Controller {
    /**
     * @Route("/zoneplan",name="tixiapp_service_zoneplan")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRoutingInformationAction(Request $request) {
        /**@var $zonePlanManagement ZonePlanManagement */
        $zonePlanManagement = $this->container->get('tixi_app.zoneplanmanagement');

        $city = $request->get('city');
        $plz = $request->get('plz');

        try {
            $zone = $zonePlanManagement->getZoneForAddressData($city, $plz);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $zoneName = '';
        if ($zone) {
            $zoneName = $zone->getName();
        }

        $response = new JsonResponse();
        $response->setData(array(
            'zone' => $zoneName
        ));
        return $response;
    }
} 