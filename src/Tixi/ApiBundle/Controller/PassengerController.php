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
use Tixi\ApiBundle\Form\PassengerType;
use Tixi\ApiBundle\Interfaces\PassengerRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Passenger\PassengerRegisterFormViewTile;

/**
 * Class PassengerController
 * @package Tixi\ApiBundle\Controller
 * @Route("/passengers")
 * @Breadcrumb("passenger.breadcrumb.name", route="tixiapi_passengers_get")
 */
class PassengerController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuPassengerId;
    }

    /**
     * @Route("", name="tixiapi_passengers_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getPassengersAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createPassengerController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'passenger.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{passengerId}", requirements={"passengerId" = "^(?!new)[^/]+$"}, name="tixiapi_passenger_get")
     * @Breadcrumb("{passengerId}", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     * @param Request $request
     * @param $passengerId
     * @return Response
     */
    public function getPassengerAction(Request $request, $passengerId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assembler = $this->get('tixi_api.assemblerpassenger');

        /**@var $passenger \Tixi\CoreDomain\Passenger */
        $passenger = $this->getPassenger($passengerId);
        $passengerDTO = $assembler->passengerToPassengerRegisterDTO($passenger);

        $gridController = $dataGridControllerFactory->createPassengerAbsentController(true, array('passengerId' => $passengerId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $gridController);

        $rootPanel = new RootPanel($this->menuId, 'passenger.panel.name', $passenger->getFirstname() . ' ' . $passenger->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('passenger.panel.details', PanelTile::$primaryType));
        $formPanel->add(new PassengerRegisterFormViewTile('passengerRequest', $passengerDTO, $this->generateUrl('tixiapi_passenger_edit', array('passengerId' => $passengerId))));
        $gridPanel = $panelSplitter->addRight(new PanelTile('absent.panel.embedded'));
        $gridPanel->add($gridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_passenger_delete', array('passengerId' => $passengerId)),'passenger.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{passengerId}/delete",name="tixiapi_passenger_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePassenger(Request $request, $passengerId) {
        $passanger = $this->getPassenger($passengerId);
        $passanger->deleteLogically();
        $this->get('entity_manager')->flush();

        return $this->redirect($this->generateUrl('tixiapi_passengers_get'));
    }

    /**
     * @Route("/new", name="tixiapi_passenger_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("passenger.panel.new", route="tixiapi_passenger_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPassengerAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $passenger = $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passenger->getId())));
        }

        $rootPanel = new RootPanel($this->menuId, 'passenger.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{passengerId}/edit", name="tixiapi_passenger_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{passengerId}", route={"name"="tixiapi_passenger_edit", "parameters"={"passengerId"}})
     * @param Request $request
     * @param $passengerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPassengerAction(Request $request, $passengerId) {
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $passengerAssembler = $this->get('tixi_api.assemblerpassenger');

        $passenger = $this->getPassenger($passengerId);
        $passengerDTO = $passengerAssembler->passengerToPassengerRegisterDTO($passenger);

        $form = $this->getForm($passengerDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId' => $passengerId)));
        }

        $gridController = $dataGridControllerFactory->createPassengerAbsentController(true, array('passengerId' => $passengerId));
        $gridTile = $dataGridHandler->createEmbeddedDataGridTile($this->menuId, $gridController);

        $rootPanel = new RootPanel($this->menuId, 'passenger.panel.name', $passenger->getFirstname() . ' ' . $passenger->getLastname());
        $panelSplitter = $rootPanel->add(new PanelSplitterTile('1:1'));
        $formPanel = $panelSplitter->addLeft(new PanelTile('passenger.panel.edit', PanelTile::$primaryType));
        $formPanel->add(new FormTile($form));
        $gridPanel = $panelSplitter->addRight(new PanelTile('absent.panel.embedded'));
        $gridPanel->add($gridTile);
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_passenger_delete', array('passengerId' => $passengerId)),'passenger.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param PassengerRegisterDTO $passengerDTO
     * @return null|object|\Tixi\CoreDomain\Passenger
     */
    protected function registerOrUpdatePassenger(PassengerRegisterDTO $passengerDTO) {
        $addressRepository = $this->get('address_repository');
        $passengerRepository = $this->get('passenger_repository');
        $assembler = $this->get('tixi_api.assemblerpassenger');
        if (empty($passengerDTO->person_id)) {
            $passenger = $assembler->registerDTOtoNewPassenger($passengerDTO);
            $addressRepository->store($passenger->getAddress());
            $passengerRepository->store($passenger);
            return $passenger;
        } else {
            $passenger = $passengerRepository->find($passengerDTO->person_id);
            $assembler->registerDTOtoPassenger($passenger, $passengerDTO);
            $addressRepository->store($passenger->getAddress());
            $passengerRepository->store($passenger);
            return $passenger;
        }
    }

    /**
     * @param null $targetRoute
     * @param null $passengerDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($passengerDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new PassengerType(
            $this->menuId, $this->get('security.context')->getToken()->getUser()),
            $passengerDTO, $options);
    }

    /**
     * @param $passengerId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getPassenger($passengerId) {
        $passengerRepository = $this->get('passenger_repository');
        $passenger = $passengerRepository->find($passengerId);
        if(null === $passenger) {
            throw $this->createNotFoundException('The passenger with id ' . $passengerId . ' does not exist');
        }
        return $passenger;
    }
}