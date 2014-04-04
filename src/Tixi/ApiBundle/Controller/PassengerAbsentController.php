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
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Passenger;

/**
 * Class PassengerAbsentController
 * @package Tixi\ApiBundle\Controller
 * @Route("/passengers/{passengerId}/absents")
 * @Breadcrumb("Fahrgast", route="tixiapi_passengers_get")
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
     * @Breadcrumb("Fahrgast {passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("Abwesenheit Details")
     */
    public function getAbsentAction(Request $request, $passengerId, $absentId) {
        $absentRepository = $this->get('absent_repository');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $passenger = $this->getPassenger($passengerId);
        $absent = $absentRepository->find($absentId);
        if (null === $absent) {
            throw $this->createNotFoundException('The absent with id ' . $absentId . ' does not exists');
        }
        $absentDTO = $this->get('tixi_api.assemblerabsent')->absentToAbsentRegisterDTO($absent);

        $rootPanel = new RootPanel('absentDetails', 'Abwesenheit Details');
        $rootPanel->add(new AbsentRegisterFormViewTile('absentRequest', $absentDTO,
            $this->generateUrl('tixiapi_passenger_absent_editbasic', array('passengerId' => $passengerId, 'absentId' => $absentId))));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_passenger_absent_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("Fahrgast {passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("Neue Abwesenheit")
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

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'Neue Abwesenheit');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{absentId}/editbasic", name="tixiapi_passenger_absent_editbasic")
     * @Method({"GET","POST"})
     * @Breadcrumb("Fahrgast {passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @Breadcrumb("Abwesenheit editieren")
     */
    public function editAbsentAction(Request $request, $passengerId, $absentId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $absentRepository = $this->get('absent_repository');
        $passengerRepository = $this->get('passenger_repository');
        $absentAssembler = $this->get('tixi_api.assemblerabsent');

        /**@var $passenger \Tixi\CoreDomain\Absent */
        $absent = $absentRepository->find($absentId);
        $passenger = $passengerRepository->find($passengerId);
        if (null === $absent) {
            throw $this->createNotFoundException('This absent does not exist');
        }
        $absentDTO = $absentAssembler->absentToAbsentRegisterDTO($absent);

        $form = $this->getForm(null, $absentDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $absentDTO = $form->getData();
            $this->registerOrUpdateAbsentToPassenger($absentDTO, $passenger);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'Abwesenheit editieren');
        $rootPanel->add(new FormTile('absentNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Passenger $passenger
     */
    protected function registerOrUpdateAbsentToPassenger(AbsentRegisterDTO $absentDTO, Passenger $passenger) {
        if (empty($absentDTO->id)) {
            $absent = Absent::registerAbsent($absentDTO->subject, $absentDTO->startDate, $absentDTO->endDate);
            $passenger->assignAbsent($absent);
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

    protected function getPassenger($passengerId) {
        $passenger = $this->get('passenger_repository')->find($passengerId);
        if (null === $passenger) {
            throw $this->createNotFoundException('The passenger with id ' . $passengerId . ' does not exists');
        }
        return $passenger;
    }
}