<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 16.04.14
 * Time: 13:21
 */

namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\ShiftTypeType;
use Tixi\ApiBundle\Interfaces\Management\ShiftTypeRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\Dispo\ShiftType;

/**
 * Class ShiftTypeController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("shifttype.panel.name", route="tixiapi_management_shifttypes_get")
 * @Route("/management/shifttypes")
 */
class ShiftTypeController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementShiftTypeId;
    }

    /**
     * @Route("",name="tixiapi_management_shifttypes_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return Response
     */
    public function getShiftTypeAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementShiftTypeController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'shifttype.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{shiftTypeId}/delete",name="tixiapi_management_shifttype_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $shiftTypeId
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShiftTypeAction(Request $request, $shiftTypeId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $shiftType = $this->getShiftType($shiftTypeId);
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $shiftRepository = $this->get('shift_repository');
        $repeatedDrivingAssertionRepository = $this->get('repeateddrivingassertion_repository');

        $usageAmount = $shiftRepository->getAmountByShiftType($shiftType);
        $usageAmount += $repeatedDrivingAssertionRepository->getAmountByShiftType($shiftType);

        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $shiftType->deleteLogically();
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_shifttypes_get'));
        }
    }

    /**
     * @Route("/new",name="tixiapi_management_shifttype_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("shifttype.panel.new", route="tixiapi_management_shifttype_new")
     * @param Request $request
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newShiftTypeAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $shiftTypeDTO = $form->getData();
            if ($this->nameAlreadyExist($shiftTypeDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateShiftType($shiftTypeDTO);
                $this->get('entity_manager')->flush();
            }
            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_shifttypes_get'));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'shifttype.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{shiftTypeId}/edit", name="tixiapi_management_shifttype_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{shiftTypeId}", route={"name"="tixiapi_management_shifttype_edit", "parameters"={"shiftTypeId"}})
     * @param Request $request
     * @param $shiftTypeId
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editShiftTypeAction(Request $request, $shiftTypeId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $shiftTypeAssembler = $this->get('tixi_api.assemblerShiftType');
        $shiftType = $this->get('shifttype_repository')->find($shiftTypeId);
        if (null === $shiftType) {
            throw $this->createNotFoundException('This shiftType does not exist');
        }
        $shiftTypeDTO = $shiftTypeAssembler->shiftTypeToShiftTypeRegisterDTO($shiftType);
        $form = $this->getForm($shiftTypeDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $shiftTypeDTO = $form->getData();
            if ($this->nameAlreadyExist($shiftTypeDTO->name) && ($shiftType->getName() != $shiftTypeDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateShiftType($shiftTypeDTO);
                $this->get('entity_manager')->flush();
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_shifttypes_get', array('shiftTypeId' => $shiftTypeId)));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'shifttype.panel.edit');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param $shiftTypeId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getShiftType($shiftTypeId) {
        $shiftTypeRepository = $this->get('shifttype_repository');
        $shiftType = $shiftTypeRepository->find($shiftTypeId);
        if (null === $shiftType) {
            throw $this->createNotFoundException('The shifttype with id ' . $shiftTypeId . ' does not exist');
        }
        return $shiftType;
    }

    /**
     * @param null $shiftTypeDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($shiftTypeDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new ShiftTypeType($this->menuId), $shiftTypeDTO, $options);
    }

    /**
     * @param ShiftTypeRegisterDTO $shiftTypeDTO
     * @return null|object|\Tixi\CoreDomain\Dispo\ShiftType
     */
    protected function registerOrUpdateShiftType(ShiftTypeRegisterDTO $shiftTypeDTO) {
        $shiftTypeRepository = $this->get('shifttype_repository');
        $shiftTypeAssembler = $this->get('tixi_api.assemblershifttype');
        if (empty($shiftTypeDTO->id)) {
            $shiftType = $shiftTypeAssembler->registerDTOtoNewShiftType($shiftTypeDTO);
            $shiftTypeRepository->store($shiftType);
            return $shiftType;
        } else {
            $shiftType = $shiftTypeRepository->find($shiftTypeDTO->id);
            $shiftTypeAssembler->registerDTOtoShiftType($shiftTypeDTO, $shiftType);
            return $shiftType;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function nameAlreadyExist($name) {
        $shiftTypeRepository = $this->get('shifttype_repository');
        if ($shiftTypeRepository->checkIfNameAlreadyExist($name)) {
            return true;
        }
        return false;
    }
} 