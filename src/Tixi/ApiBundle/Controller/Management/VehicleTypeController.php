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
 * @Route("/management/vehicletype")
 */
class VehicleTypeController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementVehicleTypeId;
    }

    /**
     * @Route("",name="tixiapi_management_vehicletypes_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getVehicleTypesAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        return new Response('test');

        $gridController = $dataGridControllerFactory->createVehicleController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'vehicle.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }




} 