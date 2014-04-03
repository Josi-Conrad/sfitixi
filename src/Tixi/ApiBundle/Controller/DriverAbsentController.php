<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\AbsentType;
use Tixi\ApiBundle\Interfaces\AbsentListDTO;
use Tixi\ApiBundle\Interfaces\AbsentRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Absent\AbsentRegisterFormViewTile;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Driver;

/**
 * Class DriverAbsentController
 * @package Tixi\ApiBundle\Controller
 * @Route("/drivers/{driverId}/absents")
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
     * @Route("/{absentId}", requirements={"absentId" = "^(?!new)[^/]+$"},
     * name="tixiapi_driver_absent_get")
     * @Method({"GET","POST"})
     */
    public function getAbsentAction(Request $request, $driverId, $absentId) {
        $absentRepository = $this->get('absent_repository');
        $assembler = $this->get('tixi_api.assemblerabsent');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $driver = $this->getDriver($driverId);
        $absent = $absentRepository->find($absentId);
        if (null === $absent) {
            throw $this->createNotFoundException('The absent with id ' . $absentId . ' does not exists');
        }
        $absentDTO = $assembler->toAbsentRegisterDTO($absent);
        $rootPanel = new RootPanel('Abwesenheit für ', $driver->getFirstname() . ' ' . $driver->getLastname());
        $rootPanel->add(new AbsentRegisterFormViewTile('absentRequest', $absentDTO,
            $this->generateUrl('tixiapi_driver_absent_editbasic', array('driverId' => $driverId, 'absentId' => $absentId))));

        $tileRenderer->render($rootPanel);
    }

    /**
     * @Route("/new", name="tixiapi_driver_absent_new")
     * @Method({"GET","POST"})
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

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'Neue Abwesenheit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/editbasic", name="tixiapi_driver_absent_editbasic")
     * @Method({"GET","POST"})
     */
    public function editAbsentAction(Request $request, $driverId, $absentId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $absentRepository = $this->get('absent_repository');
        $absentAssembler = $this->get('tixi_api.assemblerabsent');

        /**@var $driver \Tixi\CoreDomain\Absent */
        $absent = $absentRepository->find($absentId);
        if (null === $absent) {
            throw $this->createNotFoundException('This absent does not exist');
        }
        $absentDTO = $absentAssembler->toAbsentRegisterDTO($absent);

        $form = $this->getForm(null, $absentDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsent($absentDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'Neue Abwesenheit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Driver $driver
     */
    protected function registerOrUpdateAbsentToDriver(AbsentRegisterDTO $absentDTO, Driver $driver) {
        if (empty($absentDTO->id)) {
            $absent = Absent::registerAbsent($absentDTO->subject, $absentDTO->startDate, $absentDTO->endDate);
            $driver->assignAbsent($absent);
            $this->get('absent_repository')->store($absent);
        } else {
            /**@var $absent Absent */
            $absent = $this->get('absent_repository')->find($absentDTO->id);
            $absent->updateBasicData($absentDTO->subject, $absentDTO->startDate, $absentDTO->endDate);
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

    protected function getDriver($driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        if (null === $driver) {
            throw $this->createNotFoundException('The driver with id ' . $driverId . ' does not exists');
        }
        return $driver;
    }
}