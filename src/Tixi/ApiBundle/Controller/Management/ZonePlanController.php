<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:21
 */

namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\ZonePlanType;
use Tixi\ApiBundle\Interfaces\Management\ZonePlanAssembler;
use Tixi\ApiBundle\Interfaces\Management\ZonePlanRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;

/**
 * Class ZonePlanController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("zoneplan.panel.name", route="tixiapi_management_zoneplans_get")
 * @Route("/management/zoneplans")
 */
class ZonePlanController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementZonePlanId;
    }

    /**
     * @Route("",name="tixiapi_management_zoneplans_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getZonePlansAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementZonePlanController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'zoneplan.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{zonePlanId}/delete",name="tixiapi_management_zoneplan_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $zonePlanId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteZonePlanAction(Request $request, $zonePlanId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $zonePlan = $this->getZonePlan($zonePlanId);
        $zonePlan->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_management_zoneplans_get'));
    }

    /**
     * @Route("/new",name="tixiapi_management_zoneplan_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("zoneplan.panel.new", route="tixiapi_management_zoneplan_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newZonePlanAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $zonePlanDTO = $form->getData();
            $this->registerOrUpdateZonePlan($zonePlanDTO);

            try {
                $this->get('entity_manager')->flush();
            } catch (DBALException $e) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('city')->addError($error);
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_zoneplans_get'));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'zoneplan.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{zonePlanId}/edit", name="tixiapi_management_zoneplan_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{zonePlanId}", route={"name"="tixiapi_management_zoneplan_edit", "parameters"={"zonePlanId"}})
     * @param Request $request
     * @param $zonePlanId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editZonePlanAction(Request $request, $zonePlanId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var ZonePlanAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerzoneplan');

        $zonePlan = $this->getZonePlan($zonePlanId);
        $zonePlanDTO = $assembler->toZonePlanRegisterDTO($zonePlan);
        $form = $this->getForm($zonePlanDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $zonePlanDTO = $form->getData();
            $this->registerOrUpdateZonePlan($zonePlanDTO);

            try {
                $this->get('entity_manager')->flush();
            } catch (DBALException $e) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('city')->addError($error);
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_zoneplans_get'));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'zoneplan.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_zoneplan_delete', array('zonePlanId' => $zonePlanId)), 'zoneplan.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $zonePlanDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return Form
     */
    protected function getForm($zonePlanDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new ZonePlanType($this->menuId), $zonePlanDTO, $options);
    }

    /**
     * @param ZonePlanRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\POIKeyword
     */
    protected function registerOrUpdateZonePlan(ZonePlanRegisterDTO $dto) {
        /** @var ZonePlanAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerzoneplan');
        $zonePlanRepository = $this->get('zoneplan_repository');

        if (null === $dto->id) {
            $zonePlan = $assembler->registerDTOtoNewZonePlan($dto);
            $zonePlanRepository->store($zonePlan);
            return $zonePlan;
        } else {
            $zonePlan = $this->getZonePlan($dto->id);
            $assembler->registerDTOtoZonePlan($zonePlan, $dto);
            return $zonePlan;
        }
    }

    /**
     * @param $zonePlanId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getZonePlan($zonePlanId) {
        $repository = $this->get('zoneplan_repository');
        $zonePlan = $repository->find($zonePlanId);
        if (null === $zonePlan) {
            throw $this->createNotFoundException('The zonePlan with id ' . $zonePlanId . ' does not exist');
        }
        return $zonePlan;
    }


} 