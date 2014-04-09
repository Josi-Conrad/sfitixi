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
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\POI\POIRegisterFormViewTile;

/**
 * Class POIController
 * @package Tixi\ApiBundle\Controller
 * @Route("/pois")
 * @Breadcrumb("poi.panel.name", route="tixiapi_pois_get")
 */
class POIController extends Controller {
    /**
     * @Route("", name="tixiapi_pois_get")
     * @Method({"GET","POST"})
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
     */
    public function getPOIAction(Request $request, $poiId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $poi = $this->get('poi_repository')->find($poiId);
        if (null === $poi) {
            throw $this->createNotFoundException('The poi with id ' . $poiId . ' does not exists');
        }
        $poiDTO = $this->get('tixi_api.assemblerpoi')->poiToPOIRegisterDTO($poi);
        $rootPanel = new RootPanel('tixiapi_pois_get', $poi->getName());
        $rootPanel->add(new POIRegisterFormViewTile('poiRequest', $poiDTO,
            $this->generateUrl('tixiapi_poi_editbasic', array('poiId' => $poiId))));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_poi_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("poi.panel.new", route="tixiapi_poi_new")
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
     * @Route("/{poiId}/editbasic", name="tixiapi_poi_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("{poiId}", route={"name"="tixiapi_poi_editbasic", "parameters"={"poiId"}})
     */
    public function editPOIAction(Request $request, $poiId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $poiAssembler = $this->get('tixi_api.assemblerpoi');
        $poi = $this->get('poi_repository')->find($poiId);
        if (null === $poi) {
            throw $this->createNotFoundException('This poi does not exist');
        }
        $poiDTO = $poiAssembler->poiToPOIRegisterDTO($poi);
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
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param POIRegisterDTO $poiDTO
     * @return null|object|\Tixi\CoreDomain\POI
     */
    protected function registerOrUpdatePOI(POIRegisterDTO $poiDTO) {
        if (empty($poiDTO->id)) {
            $poi = $this->get('tixi_api.assemblerpoi')->registerDTOtoNewPOI($poiDTO);
            $this->get('address_repository')->store($poi->getAddress());
            $this->get('poi_repository')->store($poi);
            return $poi;
        } else {
            $poi = $this->get('poi_repository')->find($poiDTO->id);
            $this->get('tixi_api.assemblerpoi')->registerDTOtoPOI($poiDTO, $poi);
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
}