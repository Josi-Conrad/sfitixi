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
use Tixi\ApiBundle\Form\AbsentType;
use Tixi\ApiBundle\Interfaces\AbsentListDTO;
use Tixi\ApiBundle\Interfaces\AbsentRegisterDTO;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Absent\AbsentRegisterFormViewTile;
use Tixi\CoreDomain\Driver;

/**
 * Class DriverAbsentController
 * @package Tixi\ApiBundle\Controller
 * @Route("/drivers/{driverId}/absents")
 * @Breadcrumb("driver.panel.name", route="tixiapi_drivers_get")
 */
class DriverAbsentController extends Controller {

    /**
     * @Route("", name="tixiapi_driver_absents_get")
     * @Method({"GET","POST"})
     */
    public function getAbsentsAction($driverId, Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || ($request->get('embedded') !== null && $request->get('embedded'));

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDriverAbsentController($embeddedState, array('driverId' => $driverId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{absentId}", requirements={"absentId" = "^(?!new)[^/]+$"}, name="tixiapi_driver_absent_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.details")
     */
    public function getAbsentAction(Request $request, $driverId, $absentId) {
        $absentRepository = $this->get('absent_repository');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $driver = $this->getDriver($driverId);
        $absent = $absentRepository->find($absentId);
        if (null === $absent) {
            throw $this->createNotFoundException('The absent with id ' . $absentId . ' does not exists');
        }
        $absentDTO = $this->get('tixi_api.assemblerabsent')->absentToAbsentRegisterDTO($absent);

        $rootPanel = new RootPanel('absentDetail', 'absent.panel.details');
        $rootPanel->add(new AbsentRegisterFormViewTile('absentRequest', $absentDTO,
            $this->generateUrl('tixiapi_driver_absent_editbasic', array('driverId' => $driverId, 'absentId' => $absentId))));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_driver_absent_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.new")
     */
    public function newAbsentAction(Request $request, $driverId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driver = $this->getDriver($driverId);

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToDriver($absentDTO, $driver);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'absent.panel.new');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/editbasic", name="tixiapi_driver_absent_editbasic")
     * @Method({"GET","POST"})
     * @Breadcrumb(" {driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.edit")
     */
    public function editAbsentAction(Request $request, $driverId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $absentRepository = $this->get('absent_repository');
        $driverRepository = $this->get('driver_repository');
        $absentAssembler = $this->get('tixi_api.assemblerabsent');

        /**@var $driver \Tixi\CoreDomain\Absent */
        $absent = $absentRepository->find($absentId);
        $driver = $driverRepository->find($driverId);
        if (null === $absent) {
            throw $this->createNotFoundException('This absent does not exist');
        }
        $absentDTO = $absentAssembler->absentToAbsentRegisterDTO($absent);

        $form = $this->getForm(null, $absentDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToDriver($absentDTO, $driver);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'absent.panel.edit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Driver $driver
     */
    protected function registerOrUpdateAbsentToDriver(AbsentRegisterDTO $absentDTO, Driver $driver) {
        if (empty($absentDTO->id)) {
            $absent = $this->get('tixi_api.assemblerabsent')->registerDTOtoNewAbsent($absentDTO);
            $driver->assignAbsent($absent);
            $this->get('absent_repository')->store($absent);
        } else {
            $absent = $this->get('absent_repository')->find($absentDTO->id);
            $this->get('tixi_api.assemblerabsent')->registerDTOtoAbsent($absentDTO, $absent);
        }
    }

    /**
     * @param null $targetRoute
     * @param null $absentDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($targetRoute = null, $absentDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new AbsentType(), $absentDTO, $options);
    }

    /**
     * @param $driverId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDriver($driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        if (null === $driver) {
            throw $this->createNotFoundException('The driver with id ' . $driverId . ' does not exists');
        }
        return $driver;
    }
}