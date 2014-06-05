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
use Tixi\ApiBundle\Form\Management\DriverCategoryType;
use Tixi\ApiBundle\Interfaces\Management\DriverCategoryRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\DriverCategory;

/**
 * Class DriverCategoryController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("drivercategory.panel.name", route="tixiapi_management_drivercategories_get")
 * @Route("/management/drivercategories")
 */
class DriverCategoryController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementDriverCategoryId;
    }

    /**
     * @Route("",name="tixiapi_management_drivercategories_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getDriverCategorysAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementDriverCategoryController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'drivercategory.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new",name="tixiapi_management_drivercategory_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("drivercategory.panel.new", route="tixiapi_management_drivercategory_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newDriverCategoryAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverCategoryDTO = $form->getData();
            if ($this->nameAlreadyExist($driverCategoryDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateDriverCategory($driverCategoryDTO);
                $this->get('entity_manager')->flush();
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_drivercategories_get', array('drivercategoryId' => $driverCategory->getId())));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'drivercategory.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverCategoryId}/edit", name="tixiapi_management_drivercategory_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverCategoryId}", route={"name"="tixiapi_management_drivercategory_edit", "parameters"={"driverCategoryId"}})
     * @param Request $request
     * @param $driverCategoryId
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editDriverCategoryAction(Request $request, $driverCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driverCategoryAssembler = $this->get('tixi_api.assemblerDriverCategory');
        $driverCategory = $this->get('drivercategory_repository')->find($driverCategoryId);
        if (null === $driverCategory) {
            throw $this->createNotFoundException('This driverCategory does not exist');
        }
        $driverCategoryDTO = $driverCategoryAssembler->driverCategoryToDriverCategoryRegisterDTO($driverCategory);
        $form = $this->getForm($driverCategoryDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverCategoryDTO = $form->getData();
            if ($this->nameAlreadyExist($driverCategoryDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateDriverCategory($driverCategoryDTO);
                $this->get('entity_manager')->flush();
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_drivercategories_get', array('driverCategoryId' => $driverCategoryId)));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'drivercategory.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_drivercategory_delete',
            array('driverCategoryId' => $driverCategoryId)), 'drivercategory.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverCategoryId}/delete",name="tixiapi_management_drivercategory_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $driverCategoryId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDriverCategoryAction(Request $request, $driverCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $driverCategory = $this->getDriverCategory($driverCategoryId);
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driverRepository = $this->get('driver_repository');
        $usageAmount = $driverRepository->getAmountByDriverCategory($driverCategory);
        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $driverCategory->deleteLogically();
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_drivercategories_get'));
        }
    }

    /**
     * @param $driverCategoryId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDriverCategory($driverCategoryId) {
        $driverCategoryRepository = $this->get('drivercategory_repository');
        $driverCategory = $driverCategoryRepository->find($driverCategoryId);
        if (null === $driverCategory) {
            throw $this->createNotFoundException('The drivercategory with id ' . $driverCategoryId . ' does not exist');
        }
        return $driverCategory;
    }

    /**
     * @param null $driverCategoryDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($driverCategoryDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new DriverCategoryType($this->menuId), $driverCategoryDTO, $options);
    }

    /**
     * @param DriverCategoryRegisterDTO $driverCategoryDTO
     * @return \Tixi\CoreDomain\DriverCategory
     */
    protected function registerOrUpdateDriverCategory(DriverCategoryRegisterDTO $driverCategoryDTO) {
        $driverCategoryRepository = $this->get('drivercategory_repository');
        $driverCategoryAssembler = $this->get('tixi_api.assemblerdrivercategory');
        if (empty($driverCategoryDTO->id)) {
            $driverCategory = $driverCategoryAssembler->registerDTOtoNewDriverCategory($driverCategoryDTO);
            $driverCategoryRepository->store($driverCategory);
            return $driverCategory;
        } else {
            $driverCategory = $driverCategoryRepository->find($driverCategoryDTO->id);
            $driverCategoryAssembler->registerDTOtoDriverCategory($driverCategoryDTO, $driverCategory);
            return $driverCategory;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function nameAlreadyExist($name) {
        $driverCategoryRepository = $this->get('drivercategory_repository');
        if ($driverCategoryRepository->checkIfNameAlreadyExist($name)) {
            return true;
        }
        return false;
    }
} 