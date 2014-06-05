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
use Tixi\ApiBundle\Form\Management\PersonCategoryType;
use Tixi\ApiBundle\Interfaces\Management\PersonCategoryRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\PersonCategory;

/**
 * Class PersonCategoryController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("personcategory.panel.name", route="tixiapi_management_personcategories_get")
 * @Route("/management/personcategories")
 */
class PersonCategoryController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementPersonCategoryId;
    }

    /**
     * @Route("",name="tixiapi_management_personcategories_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getPersonCategorysAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementPersonCategoryController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'personcategory.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new",name="tixiapi_management_personcategory_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("personcategory.panel.new", route="tixiapi_management_personcategory_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPersonCategoryAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $personCategoryDTO = $form->getData();
            if ($this->nameAlreadyExist($personCategoryDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdatePersonCategory($personCategoryDTO);
                $this->get('entity_manager')->flush();
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_personcategories_get', array('personcategoryId' => $personCategory->getId())));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'personcategory.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{personCategoryId}/edit", name="tixiapi_management_personcategory_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{personCategoryId}", route={"name"="tixiapi_management_personcategory_edit", "parameters"={"personCategoryId"}})
     * @param Request $request
     * @param $personCategoryId
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPersonCategoryAction(Request $request, $personCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $personCategoryAssembler = $this->get('tixi_api.assemblerPersonCategory');
        $personCategory = $this->get('personcategory_repository')->find($personCategoryId);
        if (null === $personCategory) {
            throw $this->createNotFoundException('This personCategory does not exist');
        }
        $personCategoryDTO = $personCategoryAssembler->personCategoryToPersonCategoryRegisterDTO($personCategory);
        $form = $this->getForm($personCategoryDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $personCategoryDTO = $form->getData();
            if ($this->nameAlreadyExist($personCategoryDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdatePersonCategory($personCategoryDTO);
                $this->get('entity_manager')->flush();
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_personcategories_get', array('personCategoryId' => $personCategoryId)));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'personcategory.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_personcategory_delete',
            array('personCategoryId' => $personCategoryId)), 'personcategory.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{personCategoryId}/delete",name="tixiapi_management_personcategory_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $personCategoryId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePersonCategoryAction(Request $request, $personCategoryId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $personCategory = $this->getPersonCategory($personCategoryId);
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $personRepository = $this->get('person_repository');
        $usageAmount = $personRepository->getAmountByPersonCategory($personCategory);
        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $personCategory->deleteLogically();
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_personcategories_get'));
        }
    }

    /**
     * @param $personCategoryId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPersonCategory($personCategoryId) {
        $personCategoryRepository = $this->get('personcategory_repository');
        $personCategory = $personCategoryRepository->find($personCategoryId);
        if (null === $personCategory) {
            throw $this->createNotFoundException('The personcategory with id ' . $personCategoryId . ' does not exist');
        }
        return $personCategory;
    }

    /**
     * @param null $personCategoryDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($personCategoryDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new PersonCategoryType($this->menuId), $personCategoryDTO, $options);
    }

    /**
     * @param PersonCategoryRegisterDTO $personCategoryDTO
     * @return \Tixi\CoreDomain\PersonCategory
     */
    protected function registerOrUpdatePersonCategory(PersonCategoryRegisterDTO $personCategoryDTO) {
        $personCategoryRepository = $this->get('personcategory_repository');
        $personCategoryAssembler = $this->get('tixi_api.assemblerpersoncategory');
        if (empty($personCategoryDTO->id)) {
            $personCategory = $personCategoryAssembler->registerDTOtoNewPersonCategory($personCategoryDTO);
            $personCategoryRepository->store($personCategory);
            return $personCategory;
        } else {
            $personCategory = $personCategoryRepository->find($personCategoryDTO->id);
            $personCategoryAssembler->registerDTOtoPersonCategory($personCategoryDTO, $personCategory);
            return $personCategory;
        }
    }
    /**
     * @param $name
     * @return bool
     */
    protected function nameAlreadyExist($name) {
        $personCategoryRepository = $this->get('personcategory_repository');
        if ($personCategoryRepository->checkIfNameAlreadyExist($name)) {
            return true;
        }
        return false;
    }
} 