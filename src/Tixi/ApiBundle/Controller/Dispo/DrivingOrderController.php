<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.05.14
 * Time: 20:30
 */

namespace Tixi\ApiBundle\Controller\Dispo;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderCreateType;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderEditType;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderOutwardTimeException;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderReturnTimeException;
use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderRegisterDTO;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingOrderAssembler;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\DrivingOrderCreateTile;
use Tixi\ApiBundle\Tile\Dispo\DrivingOrderEditTile;
use Tixi\App\AppBundle\Interfaces\DrivingOrderHandleDTO;
use Tixi\App\Driving\DrivingOrderManagement;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlanRepository;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderRepository;
use Tixi\CoreDomain\Passenger;

/**
 * Class DrivingOrderController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/passengers/{passengerId}/orders")
 * @Breadcrumb("passenger.panel.name", route="tixiapi_passengers_get")
 */
class DrivingOrderController extends Controller{

    protected $menuId;
    protected $routingMachineSrcUrl;

    public function __construct() {
        $this->menuId = MenuService::$menuPassengerDrivingOrderId;
    }

    /**
     * @Route("", name="tixiapi_passenger_drivingorders_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @param bool $embeddedState
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDrivingOrdersForPassengerAction(Request $request, $passengerId, $embeddedState = true) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoDrivingOrderController($embeddedState, array('passengerId' => $passengerId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            // doesn't exist at the moment
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));

    }

    /**
     * @Route("/{drivingOrderId}/edit", name="tixiapi_passenger_drivingorder_edit")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @param $drivingOrderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editDrivingOrderAction(Request $request, $passengerId, $drivingOrderId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var DrivingOrderAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerdrivingorder');
        $drivingOrder = $this->getDrivingOrder($drivingOrderId);
        $editDto = $assembler->drivingOrderToEditDto($drivingOrder);

        $form = $this->createForm(new DrivingOrderEditType($this->menuId), $editDto);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $editDto = $form->getData();
            $assembler->editDTOtoDrivingOrder($editDto, $drivingOrder);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }
        $rootPanel = new RootPanel($this->menuId, 'drivingorder.panel.edit');
        /**@var $panelSplitter PanelSplitterTile */
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        /**@var $formPanel PanelTile */
        $formPanel = $panelSplitter->addLeft(new PanelTile('vehicle.panel.edit', PanelTile::$primaryType));
        $formPanel->add(new FormTile($form));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{drivingOrderId}/delete", name="tixiapi_passenger_drivingorder_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @param $drivingOrderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDrivingOrderAction(Request $request, $passengerId, $drivingOrderId) {
        /** @var DrivingOrderManagement $drivingOrderService */
        $drivingOrderService = $this->get('tixi_app.drivingordermanagement');

        $drivingOrder = $this->getDrivingOrder($drivingOrderId);
        $drivingOrderService->handleDeletionOfDrivingOrder($drivingOrder);
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_passenger_get',array('passengerId' => $passengerId)));
    }

    /**
     * @Route("/new", name="tixiapi_passenger_drivingorder_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Tixi\ApiBundle\Controller\Dispo\Response
     */
    public function newDrivingOrderAction(Request $request, $passengerId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var Passenger $passenger */
        $passenger = $this->getPassenger($passengerId);
        $form = $this->createForm(new DrivingOrderCreateType($this->menuId), new DrivingOrderRegisterDTO());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $registerDto = $form->getData();
            try {
                $this->registerDrivingOrder($registerDto, $passenger);
                $this->get('entity_manager')->flush();
                return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId'=>$passengerId)));
            }catch (DrivingOrderOutwardTimeException $e) {
                $errorMsg = $this->get('translator')->trans('drivingorder.form.outwardError');
                $form->get('orderTime')->addError(new FormError($errorMsg));
            }catch(DrivingOrderReturnTimeException $e) {
                $errorMsg = $this->get('translator')->trans('drivingorder.form.returnError');
                $form->get('orderTime')->get('returnTime')->addError(new FormError($errorMsg));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'drivingorder.panel.new',$passenger->getNameString());
        $rootPanel->add(new DrivingOrderCreateTile($form, $passengerId, $this->constructServiceUrls()));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param DrivingOrderRegisterDTO $registerDTO
     * @param Passenger $passenger
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    protected function registerDrivingOrder(DrivingOrderRegisterDTO $registerDTO, Passenger $passenger) {
        /** @var DrivingOrderAssembler $assemblerSingleDrivingOrder */
        $assemblerSingleDrivingOrder = $this->get('tixi_api.assemblerdrivingorder');
        /** @var RepeatedDrivingOrderAssembler $assemblerRepeatedDrivingOrder */
        $assemblerRepeatedDrivingOrder = $this->get('tixi_api.assemblerrepeateddrivingorder');
        /** @var RepeatedDrivingOrderPlanRepository $repeatedDrivingOrderPlanRepository */
        $repeatedDrivingOrderPlanRepository = $this->get('repeateddrivingorderplan_repository');
        /** @var RepeatedDrivingOrderRepository $repeatedDrivingOrderRepository */
        $repeatedDrivingOrderRepository = $this->get('repeateddrivingorder_repository');
        /** @var DrivingOrderManagement $drivingOrderService */
        $drivingOrderService = $this->get('tixi_app.drivingordermanagement');

        if($registerDTO->isRepeated) {
            $repeatedDrivingOrderPlan = $assemblerRepeatedDrivingOrder->registerDTOtoNewDrivingOrderPlan($registerDTO, $passenger);
            try {
                $repeatedOrders = $assemblerRepeatedDrivingOrder->registerDTOtoRepeatedDrivingOrders($registerDTO);
            }catch (DrivingOrderOutwardTimeException $e) {
                throw $e;
            }catch(DrivingOrderReturnTimeException $e) {
                throw $e;
            }

            /** @var RepeatedDrivingOrder $repeatedOrder */
            foreach($repeatedOrders as $repeatedOrder) {
                $repeatedOrder->assignRepeatedDrivingOrderPlan($repeatedDrivingOrderPlan);
                $repeatedDrivingOrderRepository->store($repeatedOrder);
            }
            $repeatedDrivingOrderPlan->replaceRepeatedDrivingOrders($repeatedOrders);
            $repeatedDrivingOrderPlanRepository->store($repeatedDrivingOrderPlan);
            $drivingOrderService->handleNewRepeatedDrivingOrder($repeatedDrivingOrderPlan);
        }else {
            try {
                $drivingOrders = $assemblerSingleDrivingOrder->registerDtoToNewDrivingOrders($registerDTO, $passenger);
            }catch(DrivingOrderOutwardTimeException $e) {
                throw $e;
            }catch(DrivingOrderReturnTimeException $e) {
                throw $e;
            }
            foreach($drivingOrders as $drivingOrder) {
                $drivingOrderService->handleNewDrivingOrder($drivingOrder);
            }
        }
    }

    /**
     * @return array
     */
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

    /**
     * @param $drivingOrderId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDrivingOrder($drivingOrderId) {
        /** @var DrivingOrderRepository $drivingOrderRepository */
        $drivingOrderRepository = $this->get('drivingorder_repository');
        $drivingOrder = $drivingOrderRepository->find($drivingOrderId);
        if(null === $drivingOrder) {
            throw $this->createNotFoundException('The drivingOrder with id ' . $drivingOrderId . ' does not exist');
        }
        return $drivingOrder;
    }
} 