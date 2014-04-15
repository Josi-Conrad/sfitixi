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
use Tixi\ApiBundle\Form\DriverType;
use Tixi\ApiBundle\Interfaces\DriverRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Driver\DriverRegisterFormViewTile;

/**
 * Class DriverController
 * @package Tixi\ApiBundle\Controller
 * @Route("/drivers")
 * @Breadcrumb("driver.breadcrumb.name", route="tixiapi_drivers_get")
 */
class DriverController extends Controller {
    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuDriverId;
    }
    /**
     * @Route("", name="tixiapi_drivers_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getDriversAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDriverController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'driver.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverId}", requirements={"driverId" = "^(?!new)[^/]+$"}, name="tixiapi_driver_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @param Request $request
     * @param $driverId
     * @return Response
     */
    public function getDriverAction(Request $request, $driverId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerdriver');

        $driver = $this->getDriver($driverId);
        $driverDTO = $assembler->driverToDriverRegisterDTO($driver);

        $absentGridController = $dataGridControllerFactory->createDriverAbsentController(true, array('driverId' => $driverId));
        $absentGridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $absentGridController);

        $repeatedAssertionPlanController = $dataGridControllerFactory->createRepeatedDrivingAssertionPlanController(true, array('driverId'=>$driverId));
        $repeatedAssertionGridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $repeatedAssertionPlanController);

        $rootPanel = new RootPanel($this->menuId, 'driver.panel.name', $driver->getFirstname().' '.$driver->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('driver.panel.details', PanelTile::$primaryType));
        $formPanel->add(new DriverRegisterFormViewTile('driverRequest', $driverDTO, $this->generateUrl('tixiapi_driver_edit', array('driverId' => $driverId))));
        $absentGridPanel = $panelSplitter->addRight(new PanelTile('absent.panel.embedded'));
        $absentGridPanel->add($absentGridTile);
        $repeatedAssertionGridPanel = $panelSplitter->addRight(new PanelTile('repeateddrivingmission.panel.embedded'));
        $repeatedAssertionGridPanel->add($repeatedAssertionGridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_driver_delete', array('driverId' => $driverId)),'driver.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverId}/delete",name="tixiapi_driver_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $driverId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDriverAction(Request $request, $driverId) {
        $driver = $this->getDriver($driverId);
        $driver->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_drivers_get'));
    }

    /**
     * @Route("/new", name="tixiapi_driver_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("driver.panel.new", route="tixiapi_driver_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newDriverAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $driver = $this->registerOrUpdateDriver($driverDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driver->getId())));
        }

        $rootPanel = new RootPanel($this->menuId, 'driver.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverId}/edit", name="tixiapi_driver_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_edit", "parameters"={"driverId"}})
     * @param Request $request
     * @param $driverId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editDriverAction(Request $request, $driverId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driverAssembler = $this->get('tixi_api.assemblerdriver');

        /**@var $driver \Tixi\CoreDomain\Driver */
        $driver = $this->getDriver($driverId);
        $driverDTO = $driverAssembler->driverToDriverRegisterDTO($driver);

        $form = $this->getForm($driverDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $this->registerOrUpdateDriver($driverDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $absentGridController = $dataGridControllerFactory->createDriverAbsentController(true, array('driverId' => $driverId));
        $absentGridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $absentGridController);

        $repeatedAssertionPlanController = $dataGridControllerFactory->createRepeatedDrivingAssertionPlanController(true, array('driverId'=>$driverId));
        $repeatedAssertionGridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $repeatedAssertionPlanController);

        $rootPanel = new RootPanel($this->menuId, 'driver.panel.name', $driver->getFirstname().' '.$driver->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('driver.panel.edit', PanelTile::$primaryType));
        $formPanel->add(new FormTile($form));
        $absentGridPanel = $panelSplitter->addRight(new PanelTile('absent.panel.embedded'));
        $absentGridPanel->add($absentGridTile);
        $repeatedAssertionGridPanel = $panelSplitter->addRight(new PanelTile('repeateddrivingmission.panel.embedded'));
        $repeatedAssertionGridPanel->add($repeatedAssertionGridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_driver_delete', array('driverId' => $driverId)),'driver.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param DriverRegisterDTO $driverDTO
     * @return null|object|\Tixi\CoreDomain\Driver
     */
    protected function registerOrUpdateDriver(DriverRegisterDTO $driverDTO) {
        if (empty($driverDTO->person_id)) {
            $driver = $this->get('tixi_api.assemblerdriver')->registerDTOtoNewDriver($driverDTO);
            $this->get('address_repository')->store($driver->getAddress());
            $this->get('driver_repository')->store($driver);
            return $driver;
        } else {
            $driver = $this->get('driver_repository')->find($driverDTO->person_id);
            $this->get('tixi_api.assemblerdriver')->registerDTOtoDriver($driverDTO, $driver);
            return $driver;
        }
    }

    /**
     * @param null $targetRoute
     * @param null $driverDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($driverDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new DriverType($this->menuId), $driverDTO, $options);
    }

    /**
     * @param $driverId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDriver($driverId) {
        $driverRepository = $this->get('driver_repository');
        $driver = $driverRepository->find($driverId);
        if(null === $driver) {
            throw $this->createNotFoundException('The driver with id ' . $driverId . ' does not exist');
        }
        return $driver;
    }
}