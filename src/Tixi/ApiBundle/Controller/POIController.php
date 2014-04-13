<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\POIType;
use Tixi\ApiBundle\Interfaces\POIRegisterDTO;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\POI\POIRegisterFormViewTile;

/**
 * Class POIController
 * @package Tixi\ApiBundle\Controller
 * @Route("/pois")
 * @Breadcrumb("poi.breadcrumb.name", route="tixiapi_pois_get")
 */
class POIController extends Controller {
    /**
     * @Route("", name="tixiapi_pois_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getPOIsAction(Request $request, $embeddedState = false) {
        $embeddedParameter = (null === $request->get('embedded') || $request->get('embedded') === 'false') ? false : true;
        $isEmbedded = ($embeddedState || $embeddedParameter);

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createPOIController($isEmbedded);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{poiId}", requirements={"poiId" = "^(?!new)[^/]+$"}, name="tixiapi_poi_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{poiId}", route={"name"="tixiapi_poi_get", "parameters"={"poiId"}})
     * @param Request $request
     * @param $poiId
     * @return Response
     */
    public function getPOIAction(Request $request, $poiId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerpoi');

        $poi = $this->getPoi($poiId);
        $poiDTO = $assembler->poiToPOIRegisterDTO($poi);
        $rootPanel = new RootPanel('tixiapi_pois_get', 'poi.panel.name', $poi->getName());
        $rootPanel->add(new POIRegisterFormViewTile('poiRequest', $poiDTO,
            $this->generateUrl('tixiapi_poi_edit', array('poiId' => $poiId)),true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_poi_delete', array('poiId' => $poiId)),'poi.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{poiId}/delete",name="tixiapi_poi_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $poiId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePOIAction(Request $request, $poiId) {
        $poi = $this->getPoi($poiId);
        $poi->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_pois_get'));
    }

    /**
     * @Route("/new", name="tixiapi_poi_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("poi.panel.new", route="tixiapi_poi_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPOIAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $poiDTO = $form->getData();
            $poi = $this->registerOrUpdatePOI($poiDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_poi_get', array('poiId' => $poi->getId())));
        }

        $rootPanel = new RootPanel('tixiapi_pois_get', 'poi.panel.new');
        $rootPanel->add(new FormTile('poiNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{poiId}/edit", name="tixiapi_poi_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{poiId}", route={"name"="tixiapi_poi_edit", "parameters"={"poiId"}})
     * @param Request $request
     * @param $poiId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPOIAction(Request $request, $poiId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerpoi');

        $poi = $this->getPoi($poiId);
        $poiDTO = $assembler->poiToPOIRegisterDTO($poi);
        $form = $this->getForm(null, $poiDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $poiDTO = $form->getData();
            $this->registerOrUpdatePOI($poiDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_poi_get', array('poiId' => $poiId)));
        }
        $rootPanel = new RootPanel('tixiapi_pois_get', 'poi.panel.edit');
        $rootPanel->add(new FormTile('poiEditForm', $form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_poi_delete', array('poiId' => $poiId)),'poi.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param POIRegisterDTO $poiDTO
     * @return null|object|\Tixi\CoreDomain\POI
     */
    protected function registerOrUpdatePOI(POIRegisterDTO $poiDTO) {
        $assembler = $this->get('tixi_api.assemblerpoi');
        $poiRepository = $this->get('poi_repository');
        $addressRepository = $this->get('address_repository');
        if (empty($poiDTO->id)) {
            $poi = $assembler->registerDTOtoNewPOI($poiDTO);
            $addressRepository->store($poi->getAddress());
            $poiRepository->store($poi);
            return $poi;
        } else {
            $poi = $poiRepository->find($poiDTO->id);
            $assembler->registerDTOtoPOI($poiDTO, $poi);
            return $poi;
        }
    }

    /**
     * @param null $targetRoute
     * @param null $poiDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($targetRoute = null, $poiDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new POIType(), $poiDTO, $options);
    }

    /**
     * @param $poiId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPoi($poiId) {
        $poiRepository = $this->get('poi_repository');
        $poi = $poiRepository->find($poiId);
        if (null === $poi) {
            throw $this->createNotFoundException('The POI with id ' . $poi . ' does not exist');
        }
        return $poi;
    }
}