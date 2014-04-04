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
use Tixi\ApiBundle\Form\PassengerType;
use Tixi\ApiBundle\Interfaces\PassengerListDTO;
use Tixi\ApiBundle\Interfaces\PassengerRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Passenger\PassengerRegisterFormViewTile;

/**
 * Class PassengerController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrgast", route="tixiapi_passengers_get")
 * @Route("/passengers")
 */
class PassengerController extends Controller {
    /**
     * @Route("", name="tixiapi_passengers_get")
     * @Method({"GET","POST"})
     * GetParameters:
     * page,limit,orderbyfield,orderbydirection
     * filterstr,partial,embedded
     * @param Request $request
     * @param bool $embeddedState
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPassengersAction(Request $request, $embeddedState = false) {
        $embeddedParameter = (null === $request->get('embedded') || $request->get('embedded') === 'false') ? false : true;
        $isEmbedded = ($embeddedState || $embeddedParameter);

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createPassengerController($isEmbedded);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{passengerId}", requirements={"passengerId" = "^(?!new)[^/]+$"},
     * name="tixiapi_passenger_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @return mixed
     * @Breadcrumb("Fahrgast Details", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     */
    public function getPassengerAction(Request $request, $passengerId) {

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        /**@var $passenger \Tixi\CoreDomain\Passenger */
        $passenger = $this->get('passenger_repository')->find($passengerId);
        $passengerDTO = $this->get('tixi_api.assemblerpassenger')->passengerToPassengerRegisterDTO($passenger);

        $gridController = $dataGridControllerFactory->createPassengerAbsentController(true, array('passengerId' => $passengerId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_passengers_get', $passenger->getFirstname().' '.$passenger->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('Fahrgast Details', PanelTile::$primaryType));
        $formPanel->add(new PassengerRegisterFormViewTile('passengerRequest', $passengerDTO, $this->generateUrl('tixiapi_passenger_editbasic', array('passengerId' => $passengerId))));
        $gridPanel = $panelSplitter->addRight(new PanelTile('Zugeordnete Abwesenheiten'));
        $gridPanel->add($gridTile);

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_passenger_new")
     * @Method({"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response
     * @Breadcrumb("Neuer Fahrgast", route="tixiapi_passenger_new")
     */
    public function newPassengerAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passengers_get'));
        }

        $rootPanel = new RootPanel('tixiapi_passengers_get', 'Neuer Fahrgast');
        $rootPanel->add(new FormTile('passengerNewForm', $form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{passengerId}/editbasic", name="tixiapi_passenger_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("Fahrgast editieren", route={"name"="tixiapi_passenger_editbasic", "parameters"={"passengerId"}})
     */
    public function editPassengerAction(Request $request, $passengerId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $passengerRepository = $this->get('passenger_repository');
        $passengerAssembler = $this->get('tixi_api.assemblerpassenger');

        /**@var $passenger \Tixi\CoreDomain\Passenger */
        $passenger = $passengerRepository->find($passengerId);
        if (null === $passenger) {
            throw $this->createNotFoundException('This passenger does not exist');
        }
        $passengerDTO = $passengerAssembler->passengerToPassengerRegisterDTO($passenger);

        $form = $this->getForm(null, $passengerDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }

        $gridController = $dataGridControllerFactory->createPassengerAbsentController(true, array('passengerId' => $passengerId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($gridController);

        $rootPanel = new RootPanel('tixiapi_passengers_get', $passenger->getFirstname().' '.$passenger->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('Fahrgast editieren', PanelTile::$primaryType));
        $formPanel->add(new FormTile('passengerForm', $form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('Zugeordnete Abwesenheiten'));
        $gridPanel->add($gridTile);

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param PassengerRegisterDTO $passengerDTO
     */
    protected function registerOrUpdatePassenger(PassengerRegisterDTO $passengerDTO) {
        if (empty($passengerDTO->person_id)) {
            $passenger = $this->get('tixi_api.assemblerpassenger')->registerDTOtoNewPassenger($passengerDTO);
            $this->get('address_repository')->store($passenger->getAddress());
            $this->get('passenger_repository')->store($passenger);
        } else {
            $passenger = $this->get('passenger_repository')->find($passengerDTO->person_id);
            $this->get('tixi_api.assemblerpassenger')->registerDTOtoPassenger($passenger, $passengerDTO);
            $this->get('address_repository')->store($passenger->getAddress());
            $this->get('passenger_repository')->store($passenger);
        }
    }

    /**
     * @param null $targetRoute
     * @param null $passengerDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($targetRoute = null, $passengerDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new PassengerType(), $passengerDTO, $options);
    }
}