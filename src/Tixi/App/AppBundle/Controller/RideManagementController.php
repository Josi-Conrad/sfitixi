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
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\App\Ride\RideManagement;

/**
 * Class RideManagementController
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class RideManagementController extends Controller {
    /**
     * Get json routing information for feasibility, for example:
     * /service/ride/feasible?day=01.06.2014&time=12:23&direction=1&duration=23&additionalTime=2
     * @Route("/ride/feasible", name="tixiapp_service_ride_feasible")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFeasibilityAction(Request $request) {
        /**@var $rideManagement RideManagement */
        $rideManagement = $this->container->get('tixi_app.ridemanagement');

        $dayStr = $request->get('day');
        $timeStr = $request->get('time');
        $dayTime = \DateTime::createFromFormat('d.m.Y H:i', $dayStr . ' ' . $timeStr);
        if (!$dayTime) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1'
            ));
            return $response;
        }

        $direction = $request->get('direction');
        $duration = $request->get('duration');
        $additionalTime = $request->get('additionalTime');

        try {
            $isFeasible = $rideManagement->checkFeasibility($dayTime, $direction, $duration, $additionalTime);
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1'
            ));
            return $response;
        }

        $response = new JsonResponse();
        $response->setData(array(
            'isFeasible' => $isFeasible
        ));

        return $response;
    }

    /**
     * Get json routing information for feasibility, for example:
     * /service/ride/repeatedFeasible?fromDate=01.06.2014&toDate=01.07.2025&weekday=1&time=12:23&direction=1&duration=23&additionalTime=2
     * @Route("/ride/repeatedFeasible", name="tixiapp_service_ride_repeated_feasible")
     * @Method({"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRepeatedFeasibilityAction(Request $request) {
        /**@var $rideManagement RideManagement */
        $rideManagement = $this->container->get('tixi_app.ridemanagement');

        $fromDateStr = $request->get('fromDate');
        $toDateStr = $request->get('toDate');
        $timeStr = $request->get('time');

        $dayTime = \DateTime::createFromFormat('d.m.Y H:i', $fromDateStr . ' ' . $timeStr);
        if ($toDateStr !== '') {
            $toDate = \DateTime::createFromFormat('d.m.Y', $toDateStr);
        } else {
            $toDate = DateTimeService::getMaxDateTime();
        }

        if (!$dayTime) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1'
            ));
            return $response;
        }

        $weekday = $request->get('weekday');
        $direction = $request->get('direction');
        $duration = $request->get('duration');
        $additionalTime = $request->get('additionalTime');

        try {
            $isFeasible = $rideManagement->checkRepeatedFeasibility($dayTime, $toDate, $weekday, $direction, $duration, $additionalTime);
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1'
            ));
            return $response;
        }

        $response = new JsonResponse();
        $response->setData(array(
            'status' => '0',
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
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1',
                'success' => false
            ));
            return $response;
        }

        try {
            $success = $rideManagement->buildOptimizedPlanForShift($shift);
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setData(array(
                'status' => '-1',
                'success' => false
            ));
            return $response;
        }

        $response = new JsonResponse();
        $response->setData(array(
            'status' => '0',
            'success' => $success
        ));

        return $response;
    }

} 