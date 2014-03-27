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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\PassengerType;
use Tixi\ApiBundle\Interfaces\PassengerListDTO;
use Tixi\ApiBundle\Interfaces\PassengerRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class PassengerController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrgast", route="tixiapi_passengers_get")
 * @Route("/passengers")
 */
class PassengerController extends Controller {

    /**
     * @Route("",name="tixiapi_passengers_get")
     * @Method({"GET","POST"})
     * GetParameters:
     * page,limit,orderbyfield,orderbydirection
     * filterstr,partial,embedded
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPassengersAction(Request $request) {
        $dataGridState = DataGridState::createByRequest($request, new PassengerListDTO());
        $passengers = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $passengersDTO = $this->get('tixi_api.assemblerpassenger')->passengersToPassengerListDTOs($passengers);
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($passengersDTO);
        $headers = $this->get('tixi_api.datagrid')->createHeaderArray(new PassengerListDTO());
        $partial = $request->get('partial');
        if (empty($partial)) {
            $template = 'TixiApiBundle:Passenger:list.html.twig';
        } else {
            $template = 'TixiApiBundle:Shared:datagrid.tablebody.html.twig';
        }
        return $this->render($template, array('rowIdPrefix' => 'passengers', 'tableHeaders' => $headers, 'tableRows' => $rows, 'totalAmountOfRows' => $totalAmount));
    }

    /**
     * @Route("/{passengerId}",requirements={"passengerId" = "^(?!new)[^/]+$"},
     * name="tixiapi_passenger_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $passengerId
     * @return mixed
     * @Breadcrumb("Fahrgast Details", route={"name"="tixiapi_passenger_get", "parameters"={"passengerId"}})
     */
    public function getPassengerAction(Request $request, $passengerId) {
        $passenger = $this->get('passenger_repository')->find($passengerId);
        $passengerDTO = $this->get('tixi_api.assemblerpassenger')->toPassengerRegisterDTO($passenger);
        return $this->render('TixiApiBundle:Passenger:get.html.twig',
            array('passenger' => $passengerDTO));
    }

    /**
     * @Route("/new",name="tixiapi_passenger_new")
     * @Method({"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response
     * @Breadcrumb("Neuer Fahgast", route="tixiapi_passenger_new")
     */
    public function newPassengerAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_passengers_get'));
        }
        return $this->render('TixiApiBundle:Passenger:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{passengerId}/editbasic",name="tixiapi_passenger_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("Fahrgast editieren", route={"name"="tixiapi_passenger_editbasic", "parameters"={"passengerId"}})
     */
    public function editPassengerAction(Request $request, $passengerId) {
        $passengerDTO = null;
        if ($request->getMethod() === 'GET') {
            $passenger = $this->get('passenger_repository')->find($passengerId);
            if (null === $passenger) {
                throw $this->createNotFoundException('Passenger does not exist');
            }
            $passengerDTO = $this->get('tixi_api.assemblerpassenger')->toPassengerRegisterDTO($passenger);
        }
        $form = $this->getForm(null, $passengerDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $passengerDTO = $form->getData();
            $this->registerOrUpdatePassenger($passengerDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_passengers_get', array('passengerId' => $passengerId)));
        }
        return $this->render('TixiApiBundle:Passenger:edit.html.twig',
            array('form' => $form->createView(), 'passenger' => $form->getData()));
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