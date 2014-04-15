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
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\VehicleAssembler;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridControls;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Vehicle\VehicleRegisterFormViewTile;
use Tixi\CoreDomain\Vehicle;


/**
 * Class VehicleController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("vehicle.breadcrumb.name", route="tixiapi_vehicles_get")
 * @Route("/vehicles")
 */
class VehicleController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuVehicleId;
    }

    /**
     * @Route("",name="tixiapi_vehicles_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getVehiclesAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createVehicleController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'vehicle.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}",requirements={"vehicleId" = "^(?!new)[^/]+$"},name="tixiapi_vehicle_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     * @param Request $request
     * @param $vehicleId
     * @return Response
     */
    public function getVehicleAction(Request $request, $vehicleId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var VehicleAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicle');

        $vehicle = $this->getVehicle($vehicleId);
        $vehicleDTO = $assembler->toVehicleRegisterDTO($vehicle);

        $gridController = $dataGridControllerFactory->createServicePlanController(true, array('vehicleId' => $vehicleId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel($this->menuId, 'vehicle.panel.name', $vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('vehicle.panel.details', PanelTile::$primaryType));

        $formPanel->add(new VehicleRegisterFormViewTile('vehicleRequest', $vehicleDTO, $this->generateUrl('tixiapi_vehicle_edit', array('vehicleId' => $vehicleId))));
        $gridPanel = $panelSplitter->addRight(new PanelTile('serviceplan.panel.embedded'));
        $gridPanel->add($gridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_vehicle_delete', array('vehicleId' => $vehicleId)),'vehicle.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}/delete",name="tixiapi_vehicle_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteVehicleAction(Request $request, $vehicleId) {
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
     * @Route("/{vehicleId}/edit",name="tixiapi_vehicle_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_edit", "parameters"={"vehicleId"}})
     * @param Request $request
     * @param $vehicleId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editVehicleAction(Request $request, $vehicleId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var VehicleAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicle');

        $vehicle = $this->getVehicle($vehicleId);
        $vehicleDTO = $assembler->toVehicleRegisterDTO($vehicle);

        $form = $this->getForm($vehicleDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get', array('vehicleId' => $vehicleId)));
        }

        $gridController = $dataGridControllerFactory->createServicePlanController(true, array('vehicleId' => $vehicleId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel($this->menuId, 'vehicle.panel.name', $vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('vehicle.panel.details', PanelTile::$primaryType));
        $formPanel->add(new FormTile($form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('vehicle.panel.serviceplans'));
        $gridPanel->add($gridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_vehicle_delete', array('vehicleId' => $vehicleId)),'vehicle.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param VehicleRegisterDTO $vehicleDTO
     * @return null|object|Vehicle
     */
    protected function registerOrUpdateVehicle(VehicleRegisterDTO $vehicleDTO) {
        $assembler = $this->get('tixi_api.assemblervehicle');
        $repository = $this->get('vehicle_repository');
        if (null === $vehicleDTO->id) {
            $vehicle = $assembler->registerDTOtoNewVehicle($vehicleDTO);
            $repository->store($vehicle);
            return $vehicle;
        } else {
            $vehicle = $repository->find($vehicleDTO->id);
            $assembler->registerDTOToVehicle($vehicle, $vehicleDTO);
            return $vehicle;
        }
    }

    /**
     * @param null $vehicleDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($vehicleDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new VehicleType($this->menuId), $vehicleDTO, $options);
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
            throw $this->createNotFoundException('The vehicle with id ' . $vehicleId . ' does not exist');
        }
        return $vehicle;
    }
}