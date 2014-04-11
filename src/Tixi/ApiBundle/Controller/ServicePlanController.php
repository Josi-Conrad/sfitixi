<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:04
 */

namespace Tixi\ApiBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\ServicePlanType;
use Tixi\ApiBundle\Interfaces\ServicePlanRegisterDTO;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\ServicePlan\ServicePlanRegisterFormViewTile;
use Tixi\CoreDomain\Vehicle;


/**
 * Class ServicePlanController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("vehicle.breadcrumb.name", route="tixiapi_vehicles_get")
 * @Route("/vehicles/{vehicleId}/serviceplans")
 */
class ServicePlanController extends Controller {
    /**
     * @Route("",name="tixiapi_serviceplans_get")
     * @Method({"GET","POST"})
     */
    public function getServiceplansAction($vehicleId, Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || ($request->get('embedded') !== null && $request->get('embedded'));

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createServicePlanController($embeddedState, array('vehicleId' => $vehicleId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{servicePlanId}",requirements={"servicePlanId" = "^(?!new)[^/]+$"},name="tixiapi_serviceplan_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     * @Breadcrumb("serviceplan.panel.details")
     */
    public function getServiceplanAction(Request $request, $vehicleId, $servicePlanId) {
        $assembler = $this->get('tixi_api.assemblerserviceplan');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $servicePlan = $this->getServicePlan($servicePlanId);
        $servicePlanDTO = $assembler->toServicePlanRegisterDTO($servicePlan);
        $rootPanel = new RootPanel('servicePlanDetail', 'serviceplan.panel.details');

        $rootPanel->add(new ServicePlanRegisterFormViewTile('servicePlanRequest', $servicePlanDTO,
            $this->generateUrl('tixiapi_serviceplan_editbasic',
                array('vehicleId' => $vehicleId, 'servicePlanId' => $servicePlanId)),true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_serviceplan_delete',
            array('vehicleId' => $vehicleId, 'servicePlanId'=>$servicePlanId)),'serviceplan.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{servicePlanId}/delete",name="tixiapi_serviceplan_delete")
     * @Method({"GET","POST"})
     */
    public function deleteServicePlanAction(Request $request, $vehicleId, $servicePlanId) {
        $servicePlan = $this->getServicePlan($servicePlanId);
        $servicePlan->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_vehicle_get',array('vehicleId' => $vehicleId)));
    }

    /**
     * @Route("/new", name="tixiapi_serviceplan_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     * @Breadcrumb("serviceplan.panel.new")
     */
    public function newServiceplanAction(Request $request, $vehicleId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $vehicle = $this->getVehicle($vehicleId);
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $servicePlanDTO = $form->getData();
            $this->registerOrUpdateServicePlanToVehicle($servicePlanDTO, $vehicle);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get', array('vehicleId' => $vehicleId)));
        }
        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'serviceplan.panel.new');
        $rootPanel->add(new FormTile('servicePlanNewForm', $form, true));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{servicePlanId}/editbasic", name="tixiapi_serviceplan_editbasic")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     * @Breadcrumb("serviceplan.panel.edit")
     */
    public function editServiceplanAction(Request $request, $vehicleId, $servicePlanId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $servicePlanRepository = $this->get('serviceplan_repository');
        $vehicleRepository = $this->get('vehicle_repository');
        $servicePlanAssembler = $this->get('tixi_api.assemblerserviceplan');

        $servicePlan = $servicePlanRepository->find($servicePlanId);
        $vehicle = $vehicleRepository->find($vehicleId);
        if (null === $servicePlan) {
            throw $this->createNotFoundException('This servicePlan does not exist');
        }
        $servicePlanDTO = $servicePlanAssembler->toServicePlanRegisterDTO($servicePlan);

        $form = $this->getForm(null, $servicePlanDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $servicePlanDTO = $form->getData();
            $this->registerOrUpdateServicePlanToVehicle($servicePlanDTO, $vehicle);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get', array('vehicleId' => $vehicleId)));
        }

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'serviceplan.panel.edit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_serviceplan_delete',
            array('vehicleId' => $vehicleId, 'servicePlanId'=>$servicePlanId)),'serviceplan.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param ServicePlanRegisterDTO $servicePlanDTO
     * @param Vehicle $vehicle
     */
    protected function registerOrUpdateServicePlanToVehicle(ServicePlanRegisterDTO $servicePlanDTO, Vehicle $vehicle) {
        if (empty($servicePlanDTO->id)) {
            $servicePlan = $this->get('tixi_api.assemblerserviceplan')->registerDTOtoNewServicePlan($servicePlanDTO);
            $vehicle->assignServicePlan($servicePlan);
            $this->get('serviceplan_repository')->store($servicePlan);
        } else {
            $servicePlan = $this->get('serviceplan_repository')->find($servicePlanDTO->id);
            $this->get('tixi_api.assemblerserviceplan')->registerDTOtoServicePlan($servicePlanDTO, $servicePlan);
        }
    }

    /**
     * @param null $targetRoute
     * @param null $servicePlanDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($targetRoute = null, $servicePlanDTO = null, $parameters = array(), $method = 'POST') {
        $options = array();
        if ($targetRoute) {
            $options['action'] = $this->generateUrl($targetRoute, $parameters);
            $options['method'] = $method;
        }
        return $this->createForm(new ServicePlanType(), $servicePlanDTO, $options);
    }

    /**
     * @param $servicePlanId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getServicePlan($servicePlanId) {
        $servicePlanRepository = $this->get('serviceplan_repository');
        $servicePlan = $servicePlanRepository->find($servicePlanId);
        if (null === $servicePlan) {
            throw $this->createNotFoundException('The ServicePlan with id ' . $servicePlan . ' does not exist');
        }
        return $servicePlan;
    }
    /**
     * @param $vehicleId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getVehicle($vehicleId) {
        $vehicleRepository = $this->get('vehicle_repository');
        $vehicle = $vehicleRepository->find($vehicleId);
        if (null === $vehicle) {
            throw $this->createNotFoundException('The Vehicle with id ' . $vehicleId . ' does not exist');
        }
        return $vehicle;
    }


} 