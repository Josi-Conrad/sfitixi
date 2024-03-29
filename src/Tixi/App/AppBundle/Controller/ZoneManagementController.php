<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.05.14
 * Time: 18:54
 */

namespace Tixi\App\AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tixi\App\AppBundle\Interfaces\ZoneAssembler;
use Tixi\App\ZonePlan\ZonePlanManagement;

/**
 * Class ZoneManagementController
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class ZoneManagementController extends Controller{

    /**
     * @Route("/zone",name="tixiapp_service_zone")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getZonePlanForCitiesAction(Request $request) {
        /** @var ZonePlanManagement $zoneManager */
        $zoneManager = $this->get('tixi_app.zoneplanmanagement');
        $cities = $request->get('cities');

        $error = false;
        $zone = null;
        if($cities){
            try {
                $zone = $zoneManager->getZoneWithHighestPriorityForCities($cities);
            }catch (\InvalidArgumentException $e) {
                $error = true;
            }
        } else {
            $error = true;
        }

        $zoneTransfer = ZoneAssembler::zoneToZoneTransferDTO($zone, $error, $this->get('translator'));
        $response = new JsonResponse();
        $response->setData($zoneTransfer->toArray());
        return $response;
    }
} 