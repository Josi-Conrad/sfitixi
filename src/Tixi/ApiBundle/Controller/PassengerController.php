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
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Handicap;
use Tixi\ApiBundle\Form\PassengerType;
use Tixi\ApiBundle\Interfaces\PassengerListDTO;
use Tixi\ApiBundle\Interfaces\PassengerRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class PassengerController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrgast", route="get_passengers")
 */
class PassengerController extends Controller {

    /**
     * @QueryParam(name="page")
     * @QueryParam(name="limit")
     * @QueryParam(name="orderbyfield")
     * @QueryParam(name="orderbydirection")
     * @QueryParam(name="filterstr")
     * @QueryParam(name="partial")
     */
    public function getPassengersAction(Request $request, ParamFetcherInterface $paramFetcher) {
        $viewHandler = $this->get('fos_rest.view_handler');
        $dataGridState = DataGridState::createByParamFetcher($paramFetcher, new PassengerListDTO());
        $passengers = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $passengersDTO = $this->get('tixi_api.assemblerpassenger')->passengersToPassengerListDTOs($passengers);
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($passengersDTO);
        $view = View::create();
        if ($viewHandler->isFormatTemplating($request->get('_format'))) {
            $headers = $this->get('tixi_api.datagrid')->createHeaderArray(new PassengerListDTO());
            $partial = $paramFetcher->get('partial');
            if (empty($partial)) {
                $view->setTemplate('TixiApiBundle:Passenger:list.html.twig');
            } else {
                $view->setTemplate('TixiApiBundle:Shared:datagrid.tablebody.html.twig');
            }
            $view->setData(array('rowIdPrefix' => 'passengers', 'tableHeaders' => $headers, 'tableRows' => $rows, 'totalAmountOfRows' => $totalAmount));
        } else {

        }
        return $viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @param $passengerId
     * @return mixed
     * @Breadcrumb("Passenger {passengerId}", route={"name"="get_passenger", "parameters"={"passengerId"}})
     */
    public function getPassengerAction(Request $request, $passengerId) {
        $passenger = $this->get('passenger_repository')->find($passengerId);
        $passengerDTO = $this->get('tixi_api.assemblerpassenger')->toPassengerRegisterDTO($passenger);
        $data = array('passenger' => $passengerDTO);
        $viewHandler = $this->get('fos_rest.view_handler');
        $view = View::create($data);
        if ($viewHandler->isFormatTemplating($request->get('_format'))) {
            $view->setTemplate('TixiApiBundle:Passenger:detail.html.twig');
        }
        return $viewHandler->handle($view);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newPassengerAction() {
        $data = $this->getForm('post_passengers');
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Passenger:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param $passengerId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editPassengerAction($passengerId) {
        $passenger = $this->get('passenger_repository')->find($passengerId);
        if (empty($passenger)) {
            throw new NotFoundHttpException();
        }
        $passengerDTO = $this->get('tixi_api.assemblerpassenger')->toPassengerRegisterDTO($passenger);
        $data = $this->getForm('post_passengers', $passengerDTO);
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Passenger:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postPassengersAction(Request $request) {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            $view = View::createRouteRedirect('get_passengers');
        } else { //not valid, show errors
            $view = View::create($form);
            $view->setTemplate('TixiApiBundle:Passenger:new.html.twig');
        }
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param PassengerRegisterDTO $passengerDTO
     */
    protected function registerOrUpdatePassenger(PassengerRegisterDTO $passengerDTO) {
        if (empty($passengerDTO->id)) {
            $passenger = $this->get('tixi_api.assemblerpassenger')->registerDTOtoNewPassenger($passengerDTO);
            $this->get('address_repository')->store($passenger->getAddress());
            $this->get('passenger_repository')->store($passenger);
        } else {
            $passenger = $this->get('passenger_repository')->find($passengerDTO->id);
            $this->get('tixi_api.assemblerpassenger')->registerDTOtoPassenger($passenger, $passengerDTO);
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