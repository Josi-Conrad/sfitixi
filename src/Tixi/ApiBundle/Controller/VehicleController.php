<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:04
 */

namespace Tixi\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Interfaces\VehicleListDTO;

use Tixi\ApiBundle\Shared\DataGrid\DataGridInputState;

use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridControls;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Tile\Vehicle\VehicleRegisterFormViewTile;


/**
 * Class VehicleController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrzeuge", route="tixiapi_vehicles_get")
 * @Route("/vehicles")
 */
class VehicleController extends Controller{

    /**
     * @Route("",name="tixiapi_vehicles_get")
     * @Method({"GET","POST"})
     * GetParameters:
     * page,limit,orderbyfield,orderbydirection
     * filterstr,partial,embedded
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getVehiclesAction(Request $request, $embeddedState=false) {
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
     * @param Request $request
     * @param $vehicleId
     * @return mixed
     * @Breadcrumb("Fahrzeug {vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     */
    public function getVehicleAction(Request $request, $vehicleId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $vehicle = $this->get('vehicle_repository')->find($vehicleId);
        $vehicleDTO = $this->get('tixi_api.assemblervehicle')->toVehicleListDTO($vehicle);

        $gridController = $dataGridControllerFactory->createServicePlanController(true, array('vehicleId'=>$vehicleId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'vehicle',$vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('7:5'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('Fahrzeugdetails',PanelTile::$primaryType));
        $formPanel->add(new VehicleRegisterFormViewTile('vehicleRequest', $vehicleDTO, $this->generateUrl('tixiapi_vehicle_editbasic',array('vehicleId'=>$vehicleId))));
        $gridPanel = $panelSplitter->addRight(new PanelTile('Zugeordnete Servicepläne'));
        $gridPanel->add($gridTile);
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new",name="tixiapi_vehicle_new")
     * @Method({"GET","POST"})
     *
     * @Breadcrumb("Neues Fahrzeug", route="tixiapi_vehicle_new")
     */
    public function newVehicleAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicles_get'));
        }

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'Neues Fahrzeug');
        $rootPanel->add(new FormTile('vehicleNewForm',$form,true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleId}/editbasic",name="tixiapi_vehicle_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("Fahrzeug {vehicleId}", route={"name"="tixiapi_vehicle_editbasic", "parameters"={"vehicleId"}})
     */
    public function editVehicleAction(Request $request, $vehicleId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $vehicleRepository = $this->get('vehicle_repository');
        $vehicleAssembler = $this->get('tixi_api.assemblervehicle');

        $vehicle = $vehicleRepository->find($vehicleId);
        if(null === $vehicle) { throw $this->createNotFoundException('The vehicle does not exist');}
        $vehicleDTO = $vehicleAssembler->toVehicleRegisterDTO($vehicle);

        $form = $this->getForm(null,$vehicleDTO);
        $form->handleRequest($request);
        if($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicles_get',array('vehicleId'=>$vehicleId)));
        }

        $gridController = $dataGridControllerFactory->createServicePlanController(true, array('vehicleId'=>$vehicleId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'Fahrzeug ',$vehicle->getName());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('7:5'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('Fahrzeugdetails',PanelTile::$primaryType));
        $formPanel->add(new FormTile('vehicleForm', $form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('Zugeordnete Servicepläne'));
        $gridPanel->add($gridTile);

        return new Response($tileRenderer->render($rootPanel));
    }

    protected function registerOrUpdateVehicle(VehicleRegisterDTO $vehicleDTO) {
        if(is_null($vehicleDTO->id)) {
            $vehicle = $this->get('tixi_api.assemblervehicle')->registerDTOtoNewVehicle($vehicleDTO);
            $this->get('vehicle_repository')->store($vehicle);
        }else {
            $vehicle = $this->get('vehicle_repository')->find($vehicleDTO->id);
            $this->get('tixi_api.assemblervehicle')->registerDTOToVehicle($vehicle, $vehicleDTO);
        }
    }

    protected function getForm($targetRoute = null, $vehicleDTO = null, $parameters=array(), $method = 'POST') {
        if($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        }else {
            $options = array();
        }
        return $this->createForm(new VehicleType(), $vehicleDTO, $options);
    }



}