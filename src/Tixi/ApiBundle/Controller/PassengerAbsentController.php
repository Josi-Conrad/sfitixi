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
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Absent\AbsentRegisterFormViewTile;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Passenger;

/**
 * Class PassengerAbsentController
 * @package Tixi\ApiBundle\Controller
 * @Route("/passengers/{passengerId}/absents")
 * @Breadcrumb("passenger.breadcrumb.name", route="tixiapi_passengers_get")
 */
class PassengerAbsentController extends Controller {

    /**
     * @Route("", name="tixiapi_passenger_absents_get")
     * @Method({"GET","POST"})
     */
    public function getAbsentsAction($passengerId, Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || ($request->get('embedded') !== null && $request->get('embedded'));

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createPassengerAbsentController($embeddedState, array('passengerId' => $passengerId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{absentId}", requirements={"absentId" = "^(?!new)[^/]+$"}, name="tixiapi_passenger_absent_get")
     * @Breadcrumb("{passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("absent.panel.name")
     */
    public function getAbsentAction(Request $request, $passengerId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerabsent');

        $absent = $this->getAbsent($absentId);
        $absentDTO = $assembler->absentToAbsentRegisterDTO($absent);

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'absent.panel.details');
        $rootPanel->add(new AbsentRegisterFormViewTile('absentRequest', $absentDTO,
            $this->generateUrl('tixiapi_passenger_absent_edit', array('passengerId' => $passengerId, 'absentId' => $absentId)),true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_passenger_absent_delete',
            array('passengerId' => $passengerId, 'absentId'=>$absentId)),'absent.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/delete",name="tixiapi_passenger_absent_delete")
     * @Method({"GET","POST"})
     */
    public function delteAbsentAction(Request $request, $passengerId, $absentId) {
        $absent = $this->getAbsent($absentId);
        $absent->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_passenger_get',array('passengerId' => $passengerId)));
    }

    /**
     * @Route("/new", name="tixiapi_passenger_absent_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("{passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("absent.panel.new")
     */
    public function newAbsentAction(Request $request, $passengerId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $passenger = $this->getPassenger($passengerId);

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToPassenger($absentDTO, $passenger);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'absent.panel.new');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/edit", name="tixiapi_passenger_absent_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("absent.panel.edit")
     */
    public function editAbsentAction(Request $request, $passengerId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $absentAssembler = $this->get('tixi_api.assemblerabsent');

        $absent = $this->getAbsent($absentId);
        /**@var $passenger \Tixi\CoreDomain\Absent */
        $passenger = $this->getPassenger($passengerId);
        $absentDTO = $absentAssembler->absentToAbsentRegisterDTO($absent);

        $form = $this->getForm(null, $absentDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToPassenger($absentDTO, $passenger);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'absent.panel.edit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_passenger_absent_delete',
            array('passengerId' => $passengerId, 'absentId'=>$absentId)),'absent.button.delete'));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Passenger $passenger
     */
    protected function registerOrUpdateAbsentToPassenger(AbsentRegisterDTO $absentDTO, Passenger $passenger) {
        $absentRepository = $this->get('absent_repository');
        $assembler = $this->get('tixi_api.assemblerabsent');
        if (empty($absentDTO->id)) {
            $absent = $assembler->registerDTOtoNewAbsent($absentDTO);
            $passenger->assignAbsent($absent);
            $absentRepository->store($absent);
        } else {
            /**@var $absent Absent */
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
     * @param $absentId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getAbsent($absentId) {
        $absentRepository = $this->get('absent_repository');
        $absent = $absentRepository->find($absentId);
        if(null === $absent) {
            throw $this->createNotFoundException('The Absent with id ' . $absentId . ' does not exists');
        }
        return $absent;
    }

    /**
     * @param $passengerId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPassenger($passengerId) {
        $passenger = $this->get('passenger_repository')->find($passengerId);
        if (null === $passenger) {
            throw $this->createNotFoundException('The passenger with id ' . $passengerId . ' does not exists');
        }
        return $passenger;
    }
}