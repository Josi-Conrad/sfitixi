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
 * @Breadcrumb("driver.panel.name", route="tixiapi_drivers_get")
 */
class DriverController extends Controller {
    /**
     * @Route("", name="tixiapi_drivers_get")
     * @Method({"GET","POST"})
     */
    public function getDriversAction(Request $request, $embeddedState = false) {
        $embeddedParameter = (null === $request->get('embedded') || $request->get('embedded') === 'false') ? false : true;
        $isEmbedded = ($embeddedState || $embeddedParameter);

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDriverController($isEmbedded);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{driverId}", requirements={"driverId" = "^(?!new)[^/]+$"}, name="tixiapi_driver_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     */
    public function getDriverAction(Request $request, $driverId) {

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerdriver');

        $driver = $this->getDriver($driverId);
        $driverDTO = $assembler->driverToDriverRegisterDTO($driver);

        $absentGridController = $dataGridControllerFactory->createDriverAbsentController(true, array('driverId' => $driverId));
        $absentGridTile = $dataGridHandler->createEmbeddedDataGridTile($absentGridController);

        $repeatedAssertionPlanController = $dataGridControllerFactory->createRepeatedDrivingAssertionPlanController(true, array('driverId'=>$driverId));
        $repeatedAssertionGridTile = $dataGridHandler->createEmbeddedDataGridTile($repeatedAssertionPlanController);

        $rootPanel = new RootPanel('tixiapi_drivers_get', $driver->getFirstname().' '.$driver->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('driver.panel.details', PanelTile::$primaryType));
        $formPanel->add(new DriverRegisterFormViewTile('driverRequest', $driverDTO, $this->generateUrl('tixiapi_driver_editbasic', array('driverId' => $driverId))));
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

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'driver.panel.new');
        $rootPanel->add(new FormTile('driverNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{driverId}/editbasic", name="tixiapi_driver_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_editbasic", "parameters"={"driverId"}})
     */
    public function editDriverAction(Request $request, $driverId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driverRepository = $this->get('driver_repository');
        $driverAssembler = $this->get('tixi_api.assemblerdriver');

        /**@var $driver \Tixi\CoreDomain\Driver */
        $driver = $driverRepository->find($driverId);
        if (null === $driver) {
            throw $this->createNotFoundException('This driver does not exist');
        }
        $driverDTO = $driverAssembler->driverToDriverRegisterDTO($driver);

        $form = $this->getForm(null, $driverDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $this->registerOrUpdateDriver($driverDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $gridController = $dataGridControllerFactory->createDriverAbsentController(true, array('driverId' => $driverId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_drivers_get', $driver->getFirstname().' '.$driver->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('driver.panel.edit', PanelTile::$primaryType));
        $formPanel->add(new FormTile('driverForm', $form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('absent.panel.embedded'));
        $gridPanel->add($gridTile);
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
    protected function getForm($targetRoute = null, $driverDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new DriverType(), $driverDTO, $options);
    }

    protected function getDriver($driverId) {
        $driverRepository = $this->get('driver_repository');
        $driver = $driverRepository->find($driverId);
        if(null === $driver) {
            throw $this->createNotFoundException('The Driver with id ' . $driverId . ' does not exist');
        }
        return $driver;
    }
}