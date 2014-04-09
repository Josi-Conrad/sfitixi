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
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridControls;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Vehicle\VehicleRegisterFormViewTile;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleRepository;


/**
 * Class VehicleController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("vehicle.panel.name", route="tixiapi_vehicles_get")
 * @Route("/vehicles")
 */
class VehicleController extends Controller {

    /**
     * @Route("",name="tixiapi_vehicles_get")
     * @Method({"GET","POST"})
     */
    public function getVehiclesAction(Request $request, $embeddedState = false) {
        $embeddedParameter = (null === $request->get('embedded') || $request->get('embedded') === 'false') ? false : true;
        $isEmbedded = ($embeddedState || $embeddedParameter);

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createVehicleController($isEmbedded);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{vehicleId}",requirements={"vehicleId" = "^(?!new)[^/]+$"},name="tixiapi_vehicle_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
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

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'vehicle.panel.name', $vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('vehicle.panel.details', PanelTile::$primaryType));

        $formPanel->add(new VehicleRegisterFormViewTile('vehicleRequest', $vehicleDTO, $this->generateUrl('tixiapi_vehicle_editbasic', array('vehicleId' => $vehicleId))));
        $gridPanel = $panelSplitter->addRight(new PanelTile('serviceplan.panel.embedded'));
        $gridPanel->add($gridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_vehicle_delete', array('vehicleId' => $vehicleId)),'vehicle.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}/delete",name="tixiapi_vehicle_delete")
     * @Method({"GET","POST"})
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
     */
    public function newVehicleAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicles_get'));
        }

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'vehicle.panel.new');
        $rootPanel->add(new FormTile('vehicleNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}/editbasic",name="tixiapi_vehicle_editbasic")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleId}", route={"name"="tixiapi_vehicle_editbasic", "parameters"={"vehicleId"}})
     */
    public function editVehicleAction(Request $request, $vehicleId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $vehicleRepository = $this->get('vehicle_repository');
        /** @var VehicleAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicle');

        $vehicle = $this->getVehicle($vehicleId);
        $vehicleDTO = $assembler->toVehicleRegisterDTO($vehicle);

        $form = $this->getForm(null, $vehicleDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get', array('vehicleId' => $vehicleId)));
        }

        $gridController = $dataGridControllerFactory->createServicePlanController(true, array('vehicleId' => $vehicleId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'vehicle.panel.name', $vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('vehicle.panel.details', PanelTile::$primaryType));
        $formPanel->add(new FormTile('vehicleForm', $form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('vehicle.panel.serviceplans'));
        $gridPanel->add($gridTile);

        return new Response($tileRenderer->render($rootPanel));
    }

    protected function registerOrUpdateVehicle(VehicleRegisterDTO $vehicleDTO) {
        /** @var VehicleAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicle');
        /** @var VehicleRepository $repository */
        $repository = $this->get('vehicle_repository');
        if (null === $vehicleDTO->id) {
            $vehicle = $assembler->registerDTOtoNewVehicle($vehicleDTO);
            $repository->store($vehicle);
        } else {
            $vehicle = $repository->find($vehicleDTO->id);
            $assembler->registerDTOToVehicle($vehicle, $vehicleDTO);
        }
    }

    protected function getForm($targetRoute = null, $vehicleDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new VehicleType(), $vehicleDTO, $options);
    }

    protected function getVehicle($vehicleId) {
        $vehicleRepository = $this->get('vehicle_repository');
        $vehicle = $vehicleRepository->find($vehicleId);
        if (null === $vehicle) {
            throw $this->createNotFoundException('The vehicle does not exist');
        }
        return $vehicle;
    }


}