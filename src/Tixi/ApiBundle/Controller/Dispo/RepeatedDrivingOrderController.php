<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.06.14
 * Time: 00:32
 */

namespace Tixi\ApiBundle\Controller\Dispo;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\RepeatedDrivingOrderEditType;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingOrderAssembler;
use Tixi\ApiBundle\Menu\MenuService;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\RepeatedAssertionTile;
use Tixi\ApiBundle\Tile\Dispo\RepeatedDrivingOrderEditTile;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderRepository;

/**
 * Class RepeatedDrivingOrderController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/passengers/{passengerId}/repeatedorderplan")
 * @Breadcrumb("passenger.panel.name", route="tixiapi_passengers_get")
 */
class RepeatedDrivingOrderController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuPassengerRepeatedDrivingOrderId;
    }

    /**
     * @Route("/{orderPlanId}/edit", name="tixiapi_driver_repeatedorderplan_edit")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @param $orderPlanId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editRepeatedOrderPlanAction(Request $request, $passengerId, $orderPlanId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var RepeatedDrivingOrderAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerrepeateddrivingorder');
        /** @var RepeatedDrivingOrderRepository $repeatedDrivingOrderRepository */
        $repeatedDrivingOrderRepository = $this->get('repeateddrivingorder_repository');

        /** @var RepeatedDrivingOrderPlan $orderPlan */
        $orderPlan = $this->getOrderPlan($orderPlanId);
        $passenger = $this->getPassenger($passengerId);

        $registerDTO = $assembler->drivingOrderPlanToRegisterDTO($orderPlan);

        $form = $this->createForm(new RepeatedDrivingOrderEditType($this->menuId), $registerDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $registerDTO = $form->getData();
            $assembler->registerDTOtoDrivingOrderPlan($registerDTO, $orderPlan);
            foreach($orderPlan->getRepeatedDrivingOrdersAsArray() as $previousOrder) {
                $repeatedDrivingOrderRepository->remove($previousOrder);
            }
            $newOrders = $assembler->registerDTOtoRepeatedDrivingOrders($registerDTO);
            /** @var RepeatedDrivingOrder $newOrder */
            foreach($newOrders as $newOrder) {
                $newOrder->assignRepeatedDrivingOrderPlan($orderPlan);
                $repeatedDrivingOrderRepository->store($newOrder);
            }
            $orderPlan->replaceRepeatedDrivingOrders($newOrders);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId'=>$passengerId)));
        }

        $rootPanel = new RootPanel($this->menuId, 'repeateddrivingorder.panel.edit');
        $rootPanel->add(new RepeatedDrivingOrderEditTile($form, $passengerId, $this->constructServiceUrls()));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_driver_repeatedorderplan_delete',
            array('passengerId' => $passengerId, 'orderPlanId'=>$orderPlanId)),'repeateddrivingorder.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{orderPlanId}/delete",name="tixiapi_driver_repeatedorderplan_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @param $orderPlanId
     */
    public function deleteRepeatedOrderPlanAction(Request $request, $passengerId, $orderPlanId) {

    }


    protected function constructServiceUrls() {
        $serviceUrls = [];
        $serviceUrls['routingMachine'] = $this->generateUrl('tixiapp_service_routing');
        $serviceUrls['zone'] = $this->generateUrl('tixiapp_service_zone');
        $serviceUrls['singleRideCheck'] = $this->generateUrl('tixiapp_service_ride_feasible');
        $serviceUrls['repeatedRideCheck'] = $this->generateUrl('tixiapp_service_ride_repeated_feasible');
        return $serviceUrls;

    }

    /**
     * @param $passengerId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPassenger($passengerId) {
        $passengerRepository = $this->get('passenger_repository');
        $passenger = $passengerRepository->find($passengerId);
        if(null === $passenger) {
            throw $this->createNotFoundException('The passenger with id ' . $passengerId . ' does not exist');
        }
        return $passenger;
    }

    protected function getOrderPlan($orderPlanId) {
        /** @var RepeatedDrivingOrderPlanRepository $repeatedDrivingOrderPlanRepository */
        $repeatedDrivingOrderPlanRepository = $this->get('repeateddrivingorderplan_repository');
        $orderPlan = $repeatedDrivingOrderPlanRepository->find($orderPlanId);
        if(null === $orderPlan) {
            throw $this->createNotFoundException('The orderplan with id ' . $orderPlanId . ' does not exist');
        }
        return $orderPlan;
    }



} 