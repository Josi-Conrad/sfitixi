<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.05.14
 * Time: 10:45
 */

namespace Tixi\ApiBundle\Controller\Dispo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Menu\MenuService;

/**
 * Class DrivingAssertionController
 * @package Tixi\ApiBundle\Controller\Dispo
 * @Route("/drivers/{driverId}/drivingassertions")
 */
class DrivingAssertionController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuDriverDrivingAssertionId;
    }

    /**
     * @Route("", name="tixiapi_driver_drivingassertions_get")
     * @param Request $request
     * @param $driverId
     * @param bool $embeddedState
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDrivingAssertionsForDriverAction(Request $request, $driverId, $embeddedState = true) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoDrivingAssertionController($embeddedState, array('driverId' => $driverId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            // doesn't exist at the moment
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{drivingAssertionId}/delete", name="tixiapi_driver_drivingassertion_delete")
     * @param Request $request
     * @param $driverId
     * @param $drivingAssertionId
     */
    public function deleteDrivingAssertionAction(Request $request, $driverId, $drivingAssertionId) {
        $repository = $this->get('drivingassertion_repository');
        $drivingAssertion = $this->getDrivingAssertionById($drivingAssertionId);
        $drivingAssertion->deletePhysically();
        $repository->remove($drivingAssertion);
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_driver_get',array('driverId' => $driverId)));
    }

    public function getDrivingAssertionById($drivingAssertionId) {
        $drivingAssertion = $this->get('drivingassertion_repository')->find($drivingAssertionId);
        if (null === $drivingAssertion) {
            throw $this->createNotFoundException('The driving assertion with id ' . $drivingAssertionId . ' does not exists');
        }
        return $drivingAssertion;
    }

} 