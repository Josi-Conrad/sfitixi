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
use Tixi\ApiBundle\Form\Management\HandicapType;
use Tixi\ApiBundle\Interfaces\Management\HandicapAssembler;
use Tixi\ApiBundle\Interfaces\Management\HandicapRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;

/**
 * Class HandicapController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("handicap.breadcrumb.name", route="tixiapi_management_handicaps_get")
 * @Route("/management/handicaps")
 */
class HandicapController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementHandicapId;
    }

    /**
     * @Route("",name="tixiapi_management_handicaps_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getHandicapsAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementHandicapController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'handicap.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{handicapId}/delete",name="tixiapi_management_handicap_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $handicapId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteHandicapAction(Request $request, $handicapId) {
        $handicap = $this->getHandicap($handicapId);
        $handicap->deleteLogically();
        $this->get('entity_manager')->flush();

        return $this->redirect($this->generateUrl('tixiapi_management_handicaps_get'));
    }

    /**
     * @Route("/new",name="tixiapi_management_handicap_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("handicap.panel.new", route="tixiapi_management_handicap_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newHandicapAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $handicapDTO = $form->getData();
            $this->registerOrUpdateHandicap($handicapDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_handicaps_get'));
        }

        $rootPanel = new RootPanel($this->menuId, 'handicap.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{handicapId}/edit", name="tixiapi_management_handicap_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{handicapId}", route={"name"="tixiapi_management_handicap_edit", "parameters"={"handicapId"}})
     * @param Request $request
     * @param $handicapId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editHandicapAction(Request $request, $handicapId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var HandicapAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerhandicap');

        $handicap = $this->getHandicap($handicapId);
        $handicapDTO = $assembler->toHandicapRegisterDTO($handicap);
        $form = $this->getForm($handicapDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $handicapDTO = $form->getData();
            $this->registerOrUpdateHandicap($handicapDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_handicaps_get'));
        }
        $rootPanel = new RootPanel($this->menuId, 'handicap.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_handicap_delete', array('handicapId' => $handicapId)),'handicap.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $handicapDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    protected function getForm($handicapDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new HandicapType($this->menuId), $handicapDTO, $options);
    }

    /**
     * @param HandicapRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\POIKeyword
     */
    protected function registerOrUpdateHandicap(HandicapRegisterDTO $dto) {
        /** @var HandicapAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerhandicap');
        $repository = $this->get('handicap_repository');
        if(null === $dto->id) {
            $handicap = $assembler->registerDTOtoNewHandicap($dto);
            $repository->store($handicap);
            return $handicap;
        }else {
            $handicap = $this->getHandicap($dto->id);
            $assembler->registerDTOtoHandicap($handicap, $dto);
            return $handicap;
        }
    }

    /**
     * @param $handicapId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getHandicap($handicapId) {
        $repository = $this->get('handicap_repository');
        $handicap = $repository->find($handicapId);
        if (null === $handicap) {
            throw $this->createNotFoundException('The handicap with id ' . $handicapId . ' does not exist');
        }
        return $handicap;
    }


} 