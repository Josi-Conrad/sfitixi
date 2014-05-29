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
use Tixi\App\Ride\RideManagement;

/**
 * Class RideManagementController
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class RideManagementController extends Controller {
    /**
     * Get json routing information for feasibility, for example:
     * /service/ride/feasible?day=01.06.2014&time=12.23&direction=1&duration=23&additionalTime=2
     * @Route("/ride/feasible", name="tixiapp_service_ride_feasible")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRideFeasibilityAction(Request $request) {
        /**@var $rideManagement RideManagement */
        $rideManagement = $this->container->get('tixi_app.ridemanagement');

        $dayStr = $request->get('day');
        $timeStr = $request->get('time');
        $dayTime = \DateTime::createFromFormat('d.m.Y H.i', $dayStr . ' ' . $timeStr);
        if (!$dayTime) {
            return new Response('wrong day or time parameters', 500);
        }

        $direction = $request->get('direction');
        $duration = $request->get('duration');
        $additionalTime = $request->get('additionalTime');

        try {
            $isFeasible = $rideManagement->checkFeasibility($dayTime, $direction, $duration, $additionalTime);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $response = new JsonResponse();
        $response->setData(array(
            'isFeasible' => $isFeasible
        ));

        return $response;
    }

    /**
     * starts optimization for one shift, return false if it fails, for example:
     * /service/ride/optimize?shiftId=3
     * @Route("/ride/optimize", name="tixiapp_service_ride_optimize")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOptimizationAction(Request $request) {
        /**@var $rideManagement RideManagement */
        $rideManagement = $this->container->get('tixi_app.ridemanagement');
        $shiftRepo = $this->container->get('shift_repository');

        $shiftId = $request->get('shiftId');
        $shift = $shiftRepo->find($shiftId);

        if (!$shift) {
            return new Response('shift not found', 500);
        }

        try {
            $success = $rideManagement->getOptimizedPlanForShift($shift);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $response = new JsonResponse();
        $response->setData(array(
            'success' => $success
        ));

        return $response;
    }

} 