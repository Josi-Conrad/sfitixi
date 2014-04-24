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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\VehicleCategoryType;
use Tixi\ApiBundle\Interfaces\Management\VehicleCategoryAssembler;
use Tixi\ApiBundle\Interfaces\Management\VehicleCategoryRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine;

/**
 * Class VehicleTypeController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("vehiclecategory.breadcrumb.name", route="tixiapi_management_vehiclecategories_get")
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
     * @throws AccessDeniedException
     * @return Response
     */
    public function getVehicleCategoriesAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementVehicleTypeController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'vehiclecategory.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleCategoryId}/delete",name="tixiapi_management_vehiclecategory_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleCategoryId
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteVehicleCategoryAction(Request $request, $vehicleCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var VehicleRepositoryDoctrine $vehicleCategoryRepository */
        $vehicleRepository = $this->get('vehicle_repository');
        $vehicleCategory = $this->getVehicleCategory($vehicleCategoryId);
        $usageAmount = $vehicleRepository->getAmountByVehicleCategory($vehicleCategory);
        //it is not allowed to delete an enitity of this category that is still in use.
        if($usageAmount>0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        }else {
            $vehicleCategory->deleteLogically();
            $this->get('entity_manager')->flush();

            return $this->redirect($this->generateUrl('tixiapi_management_vehiclecategories_get'));
        }
    }

    /**
     * @Route("/new",name="tixiapi_management_vehiclecategory_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("vehiclecategory.panel.new", route="tixiapi_management_vehiclecategory_new")
     * @param Request $request
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newVehicleCategoryAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleCategoryDTO = $form->getData();
            $this->registerOrUpdateVehicleCategory($vehicleCategoryDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_vehiclecategories_get'));
        }

        $rootPanel = new RootPanel($this->menuId, 'vehiclecategory.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{vehicleCategoryId}/edit", name="tixiapi_management_vehiclecategory_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{vehicleCategoryId}", route={"name"="tixiapi_management_vehiclecategory_edit", "parameters"={"vehicleCategoryId"}})
     * @param Request $request
     * @param $vehicleCategoryId
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editVehicleCategoryAction(Request $request, $vehicleCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var VehicleCategoryAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehiclecategory');

        $vehicleCategory = $this->getVehicleCategory($vehicleCategoryId);
        $vehicleCategoryDTO = $assembler->toVehicleCategoryRegisterDTO($vehicleCategory);
        $form = $this->getForm($vehicleCategoryDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $vehicleCategoryDTO = $form->getData();
            $this->registerOrUpdateVehicleCategory($vehicleCategoryDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_vehiclecategories_get'));
        }
        $rootPanel = new RootPanel($this->menuId, 'vehiclecategory.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_vehiclecategory_delete', array('vehicleCategoryId' => $vehicleCategoryId)),'vehiclecategory.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $vehicleCategoryDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($vehicleCategoryDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new VehicleCategoryType($this->menuId), $vehicleCategoryDTO, $options);
    }

    /**
     * @param VehicleCategoryRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\VehicleCategory
     */
    protected function registerOrUpdateVehicleCategory(VehicleCategoryRegisterDTO $dto) {
        /** @var VehicleCategoryAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblervehiclecategory');
        $repository = $this->get('vehiclecategory_repository');
        if(null === $dto->id) {
            $vehicleCategory = $assembler->registerDTOtoNewVehicleCategory($dto);
            $repository->store($vehicleCategory);
            return $vehicleCategory;
        }else {
            $vehicleCategory = $this->getVehicleCategory($dto->id);
            $assembler->registerDTOtoVehicleCategory($vehicleCategory, $dto);
            return $vehicleCategory;
        }
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