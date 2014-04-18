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
use Tixi\ApiBundle\Form\Management\PoiKeywordType;
use Tixi\ApiBundle\Interfaces\Management\PoiKeywordAssembler;
use Tixi\ApiBundle\Interfaces\Management\PoiKeywordRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;

/**
 * Class PoiKeywordController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("poikeyword.breadcrumb.name", route="tixiapi_management_poikeywords_get")
 * @Route("/management/poikeywords")
 */
class PoiKeywordController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementPoiKeywordsId;
    }

    /**
     * @Route("",name="tixiapi_management_poikeywords_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getPoiKeywordsAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementPoiKeywordController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'poikeyword.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{poiKeywordId}/delete",name="tixiapi_management_poikeyword_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $poiKeywordId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePoiKeywordAction(Request $request, $poiKeywordId) {
        $vehicle = $this->getPoiKeyword($poiKeywordId);
        $vehicle->deleteLogically();
        $this->get('entity_manager')->flush();

        return $this->redirect($this->generateUrl('tixiapi_management_poikeywords_get'));
    }

    /**
     * @Route("/new",name="tixiapi_management_poikeyword_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("poikeyword.panel.new", route="tixiapi_management_poikeyword_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPoiKeywordAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $poiKeywordDTO = $form->getData();
            $this->registerOrUpdatePoiKeyword($poiKeywordDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_poikeywords_get'));
        }

        $rootPanel = new RootPanel($this->menuId, 'poikeyword.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{poiKeywordId}/edit", name="tixiapi_management_poikeyword_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{poiKeywordId}", route={"name"="tixiapi_management_poikeyword_edit", "parameters"={"poiKeywordId"}})
     * @param Request $request
     * @param $poiKeywordId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPoiKeywordAction(Request $request, $poiKeywordId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var PoiKeywordAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerpoikeyword');

        $poiKeyword = $this->getPoiKeyword($poiKeywordId);
        $poiKeywordDTO = $assembler->toPoiKeywordRegisterDTO($poiKeyword);
        $form = $this->getForm($poiKeywordDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $poiKeywordDTO = $form->getData();
            $this->registerOrUpdatePoiKeyword($poiKeywordDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_poikeywords_get'));
        }
        $rootPanel = new RootPanel($this->menuId, 'poikeyword.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_poikeyword_delete', array('poiKeywordId' => $poiKeywordId)),'poikeyword.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $poiKeywordDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    protected function getForm($poiKeywordDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new PoiKeywordType($this->menuId), $poiKeywordDTO, $options);
    }

    /**
     * @param PoiKeywordRegisterDTO $dto
     * @return mixed|\Tixi\CoreDomain\POIKeyword
     */
    protected function registerOrUpdatePoiKeyword(PoiKeywordRegisterDTO $dto) {
        /** @var PoiKeywordAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerpoikeyword');
        $repository = $this->get('poikeyword_repository');
        if(null === $dto->id) {
            $poiKeyword = $assembler->registerDTOtoNewPoiKeyword($dto);
            $repository->store($poiKeyword);
            return $poiKeyword;
        }else {
            $poiKeyword = $this->getPoiKeyword($dto->id);
            $assembler->registerDTOtoPoiKeyword($poiKeyword, $dto);
            return $poiKeyword;
        }
    }

    /**
     * @param $poiKeywordId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPoiKeyword($poiKeywordId) {
        $repository = $this->get('poikeyword_repository');
        $poiKeyword = $repository->find($poiKeywordId);
        if (null === $poiKeyword) {
            throw $this->createNotFoundException('The poi-keyword with id ' . $poiKeywordId . ' does not exist');
        }
        return $poiKeyword;
    }



} 