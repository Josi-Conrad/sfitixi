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
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\CustomFormView\AbsentRegisterFormViewTile;
use Tixi\CoreDomain\Driver;

/**
 * Class DriverAbsentController
 * @package Tixi\ApiBundle\Controller
 * @Route("/drivers/{driverId}/absents")
 * @Breadcrumb("driver.breadcrumb.name", route="tixiapi_drivers_get")
 */
class DriverAbsentController extends Controller {
    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuDriverAbsentId;
    }
    /**
     * @Route("", name="tixiapi_driver_absents_get")
     * @Method({"GET","POST"})
     * @param $driverId
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getAbsentsAction($driverId, Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDriverAbsentController($embeddedState, array('driverId' => $driverId));
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
     * @Route("/{absentId}", requirements={"absentId" = "^(?!new)[^/]+$"}, name="tixiapi_driver_absent_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.details")
     * @param Request $request
     * @param $driverId
     * @param $absentId
     * @return Response
     */
    public function getAbsentAction(Request $request, $driverId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerabsent');

        $absent = $this->getAbsent($absentId);
        $absentDTO = $assembler->absentToAbsentRegisterDTO($absent);

        $rootPanel = new RootPanel($this->menuId, 'absent.panel.details');
        $rootPanel->add(new AbsentRegisterFormViewTile('absentRequest', $absentDTO,
            $this->generateUrl('tixiapi_driver_absent_edit', array('driverId' => $driverId, 'absentId' => $absentId)),true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_driver_absent_delete',
            array('driverId' => $driverId, 'absentId'=>$absentId)),'absent.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/delete",name="tixiapi_driver_absent_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $driverId
     * @param $absentId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAbsentAction(Request $request, $driverId, $absentId) {
        $absent = $this->getAbsent($absentId);
        $absent->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_driver_get',array('driverId' => $driverId)));
    }

    /**
     * @Route("/new", name="tixiapi_driver_absent_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.new")
     * @param Request $request
     * @param $driverId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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

        $rootPanel = new RootPanel($this->menuId, 'absent.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/edit", name="tixiapi_driver_absent_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb(" {driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("absent.panel.edit")
     * @param Request $request
     * @param $driverId
     * @param $absentId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAbsentAction(Request $request, $driverId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $absentAssembler = $this->get('tixi_api.assemblerabsent');

        /**@var $driver \Tixi\CoreDomain\Absent */
        $absent = $this->getAbsent($absentId);
        $driver = $this->getDriver($driverId);
        $absentDTO = $absentAssembler->absentToAbsentRegisterDTO($absent);

        $form = $this->getForm($absentDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToDriver($absentDTO, $driver);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId' => $driverId)));
        }

        $rootPanel = new RootPanel($this->menuId, 'absent.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_driver_absent_delete',
            array('driverId' => $driverId, 'absentId'=>$absentId)),'absent.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Driver $driver
     */
    protected function registerOrUpdateAbsentToDriver(AbsentRegisterDTO $absentDTO, Driver $driver) {
        $absentRepository = $this->get('absent_repository');
        $assembler = $this->get('tixi_api.assemblerabsent');
        if (empty($absentDTO->id)) {
            $absent = $assembler->registerDTOtoNewAbsent($absentDTO);
            $driver->assignAbsent($absent);
            $absentRepository->store($absent);
        } else {
            $absent = $absentRepository->find($absentDTO->id);
            $assembler->registerDTOtoAbsent($absentDTO, $absent);
        }
    }

    /**
     * @param null $targetRoute
     * @param null $absentDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($absentDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new AbsentType($this->menuId), $absentDTO, $options);
    }

    /**
     * @param $absentId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getAbsent($absentId) {
        $absentRepository = $this->get('absent_repository');
        $absent = $absentRepository->find($absentId);
        if(null === $absent) {
            throw $this->createNotFoundException('The absent with id ' . $absentId . ' does not exists');
        }
        return $absent;
    }

    /**
     * @param $driverId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDriver($driverId) {
        $driverRepository = $this->get('driver_repository');
        $driver = $driverRepository->find($driverId);
        if (null === $driver) {
            throw $this->createNotFoundException('The driver with id ' . $driverId . ' does not exists');
        }
        return $driver;
    }
}