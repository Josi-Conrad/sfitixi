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
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\ZoneType;
use Tixi\ApiBundle\Interfaces\Management\ZoneAssembler;
use Tixi\ApiBundle\Interfaces\Management\ZoneRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\Zone;

/**
 * Class ZoneController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("zone.panel.name", route="tixiapi_management_zones_get")
 * @Route("/management/zones")
 */
class ZoneController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementZoneId;
    }

    /**
     * @Route("",name="tixiapi_management_zones_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getZonesAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementZoneController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'zone.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{zoneId}/delete",name="tixiapi_management_zone_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $zoneId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteZoneAction(Request $request, $zoneId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $zone = $this->getZone($zoneId);
        $zoneRepository = $this->get('zoneplan_repository');
        $usageAmount = $zoneRepository->getAmountByZone($zone);
        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $zone->deleteLogically();
            $this->get('entity_manager')->flush();

            return $this->redirect($this->generateUrl('tixiapi_management_zones_get'));
        }
    }

    /**
     * @Route("/new",name="tixiapi_management_zone_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("zone.panel.new", route="tixiapi_management_zone_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newZoneAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $zoneDTO = $form->getData();
            if ($this->zoneNameAlreadyExist($zoneDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateZone($zoneDTO);
                $this->get('entity_manager')->flush();
            }
            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_zones_get'));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'zone.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{zoneId}/edit", name="tixiapi_management_zone_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{zoneId}", route={"name"="tixiapi_management_zone_edit", "parameters"={"zoneId"}})
     * @param Request $request
     * @param $zoneId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editZoneAction(Request $request, $zoneId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var ZoneAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerzone');

        $zone = $this->getZone($zoneId);
        $zoneDTO = $assembler->toZoneRegisterDTO($zone);
        $form = $this->getForm($zoneDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $zoneDTO = $form->getData();
            if ($zoneDTO->name !== $zone->getName() && $this->zoneNameAlreadyExist($zoneDTO->name)) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            } else {
                $this->registerOrUpdateZone($zoneDTO);
                $this->get('entity_manager')->flush();
            }
            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_zones_get'));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'zone.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_zone_delete', array('zoneId' => $zoneId)), 'zone.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $zoneDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    protected function getForm($zoneDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new ZoneType($this->menuId), $zoneDTO, $options);
    }

    /**
     * @param ZoneRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\POIKeyword
     */
    protected function registerOrUpdateZone(ZoneRegisterDTO $dto) {
        /** @var ZoneAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerzone');
        $zoneRepository = $this->get('zone_repository');

        if (null === $dto->id) {
            $zone = $assembler->registerDTOtoNewZone($dto);
            $zoneRepository->store($zone);
            return $zone;
        } else {
            $zone = $this->getZone($dto->id);
            $assembler->registerDTOtoZone($zone, $dto);
            return $zone;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    protected function zoneNameAlreadyExist($name) {
        $zoneRepository = $this->get('zone_repository');
        if ($zoneRepository->checkIfNameAlreadyExist($name)) {
            return true;
        }
        return false;
    }

    /**
     * @param $zoneId
     * @return Zone
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getZone($zoneId) {
        $repository = $this->get('zone_repository');
        $zone = $repository->find($zoneId);
        if (null === $zone) {
            throw $this->createNotFoundException('The zone with id ' . $zoneId . ' does not exist');
        }
        return $zone;
    }


} 