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
use Tixi\ApiBundle\Form\Management\VehicleDepotType;
use Tixi\ApiBundle\Interfaces\Management\VehicleDepotAssembler;
use Tixi\ApiBundle\Interfaces\Management\VehicleDepotRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;

/**
 * Class VehicleDepotController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("vehicledepot.breadcrumb.name", route="tixiapi_management_vehicledepots_get")
 * @Route("/management/vehicledepots")
 */
class VehicleDepotController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementVehicleDepotId;
    }

    /**
     * @Route("",name="tixiapi_management_vehicledepots_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getVehicleDepotsAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementVehicleDepotController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'vehicledepot.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleDepotId}/delete",name="tixiapi_management_vehicledepot_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleDepotId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteVehicleDepotAction(Request $request, $vehicleDepotId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $vehicleDepot = $this->getVehicleDepot($vehicleDepotId);
        $vehicleRepository = $this->get('vehicle_repository');
        $usageAmount = $vehicleRepository->getAmountByVehicleDepot($vehicleDepot);
        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $vehicleDepot->deleteLogically();
            $this->get('entity_manager')->flush();

            return $this->redirect($this->generateUrl('tixiapi_management_vehicledepots_get'));
        }
    }

    /**
     * @Route("/new",name="tixiapi_management_vehicledepot_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("vehicledepot.panel.new", route="tixiapi_management_vehicledepot_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newVehicleDepotAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDepotDTO = $form->getData();
            $this->registerOrUpdateVehicleDepot($vehicleDepotDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_vehicledepots_get'));
        }

        $rootPanel = new RootPanel($this->menuId, 'vehicledepot.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleDepotId}/edit", name="tixiapi_management_vehicledepot_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleDepotId}", route={"name"="tixiapi_management_vehicledepot_edit", "parameters"={"vehicleDepotId"}})
     * @param Request $request
     * @param $vehicleDepotId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editVehicleDepotAction(Request $request, $vehicleDepotId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var VehicleDepotAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicledepot');

        $vehicleDepot = $this->getVehicleDepot($vehicleDepotId);
        $vehicleDepotDTO = $assembler->toVehicleDepotRegisterDTO($vehicleDepot);
        $form = $this->getForm($vehicleDepotDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleDepotDTO = $form->getData();
            $this->registerOrUpdateVehicleDepot($vehicleDepotDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_vehicledepots_get'));
        }
        $rootPanel = new RootPanel($this->menuId, 'vehicledepot.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_vehicledepot_delete', array('vehicleDepotId' => $vehicleDepotId)), 'vehicledepot.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $vehicleDepotDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    protected function getForm($vehicleDepotDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new VehicleDepotType($this->menuId), $vehicleDepotDTO, $options);
    }

    /**
     * @param VehicleDepotRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\POIKeyword
     */
    protected function registerOrUpdateVehicleDepot(VehicleDepotRegisterDTO $dto) {
        /** @var VehicleDepotAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehicledepot');
        $addressRepository = $this->get('address_repository');
        $vehicleDepotRepository = $this->get('vehicledepot_repository');

        if (null === $dto->id) {
            $vehicleDepot = $assembler->registerDTOtoNewVehicleDepot($dto);
            $addressRepository->store($vehicleDepot->getAddress());
            $vehicleDepotRepository->store($vehicleDepot);
            return $vehicleDepot;
        } else {
            $vehicleDepot = $this->getVehicleDepot($dto->id);
            $assembler->registerDTOtoVehicleDepot($vehicleDepot, $dto);
            return $vehicleDepot;
        }
    }

    /**
     * @param $vehicleDepotId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getVehicleDepot($vehicleDepotId) {
        $repository = $this->get('vehicledepot_repository');
        $vehicleDepot = $repository->find($vehicleDepotId);
        if (null === $vehicleDepot) {
            throw $this->createNotFoundException('The vehicledepot with id ' . $vehicleDepotId . ' does not exist');
        }
        return $vehicleDepot;
    }


} 