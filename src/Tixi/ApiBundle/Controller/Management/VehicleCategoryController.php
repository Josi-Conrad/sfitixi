<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:21
 */

namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Menu\MenuService;

/**
 * Class VehicleTypeController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("vehicle.breadcrumb.name", route="tixiapi_vehicles_get")
 * @Route("/management/vehiclecategories")
 */
class VehicleCategoryController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementVehicleCategoryId;
    }

    /**
     * @Route("",name="tixiapi_management_vehiclecategories_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getVehicleCategoryAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementVehicleTypeController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'vehicletype.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}/delete",name="tixiapi_management_vehiclecategories_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteVehicleCategoryAction(Request $request, $vehicleId) {
        $vehicle = $this->getVehicle($vehicleId);
        $vehicle->deleteLogically();
        $this->get('entity_manager')->flush();

        return $this->redirect($this->generateUrl('tixiapi_vehicles_get'));
    }

    /**
     * @Route("/new",name="tixiapi_vehicle_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("vehicle.panel.new", route="tixiapi_vehicle_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newVehicleAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDTO = $form->getData();
            $vehicle = $this->registerOrUpdateVehicle($vehicleDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get', array('vehicleId' => $vehicle->getId())));
        }

        $rootPanel = new RootPanel($this->menuId, 'vehicle.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param $vehicleCategoryId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getVehicleCategory($vehicleCategoryId) {
        $vehicleCategoryRepository = $this->get('vehiclecategory_repository');
        $vehicleCategory = $vehicleCategoryRepository->find($vehicleCategoryId);
        if (null === $vehicleCategory) {
            throw $this->createNotFoundException('The vehicle category with id ' . $vehicleCategoryId . ' does not exist');
        }
        return $vehicleCategory;

    }



} 