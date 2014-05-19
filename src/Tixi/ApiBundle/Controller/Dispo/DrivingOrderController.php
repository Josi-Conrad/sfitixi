<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.05.14
 * Time: 20:30
 */

namespace Tixi\ApiBundle\Controller\Dispo;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\DrivingOrderType;
use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\DrivingOrderTile;
use Tixi\CoreDomain\Passenger;

/**
 * Class DrivingOrderController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/passengers/{passengerId}/order")
 * @Breadcrumb("passenger.panel.name", route="tixiapi_passengers_get")
 */
class DrivingOrderController extends Controller{

    protected $menuId;
    protected $routingMachineSrcUrl;

    public function __construct() {
        $this->menuId = MenuService::$menuPassengerDrivingOrderId;
    }

    /**
     * @Route("/new", name="tixiapi_passenger_drivingorder_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Tixi\ApiBundle\Controller\Dispo\Response
     */
    public function newDrivingOrderAction(Request $request, $passengerId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var Passenger $passenger */
        $passenger = $this->getPassenger($passengerId);
        $form = $this->createForm(new DrivingOrderType($this->menuId), new DrivingOrderRegisterDTO());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $registerDto = $form->getData();
            $this->registerOrUpdateDrivingOrder($registerDto, $passenger);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passenger_get', array('passengerId'=>$passengerId)));
        }

        $rootPanel = new RootPanel($this->menuId, 'drivingorder.panel.new');
        $rootPanel->add(new DrivingOrderTile($form, $passengerId, $this->generateUrl('tixiapp_service_routing')));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param DrivingOrderRegisterDTO $registerDto
     * @param Passenger $passenger
     */
    protected function registerOrUpdateDrivingOrder(DrivingOrderRegisterDTO $registerDto, Passenger $passenger) {

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