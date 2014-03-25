<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:04
 */

namespace Tixi\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Interfaces\VehicleListDTO;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * Class VehicleController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrzeuge", route="tixiapi_vehicles_get")
 * @Route("/vehicles")
 */
class VehicleController extends Controller{

    /**
     * @Route("",name="tixiapi_vehicles_get")
     * @Method({"GET","POST"})
     * GetParameters:
     * page,limit,orderbyfield,orderbydirection
     * filterstr,partial,embedded
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getVehiclesAction(Request $request) {
        $dataGridState = DataGridState::createByRequest($request, new VehicleListDTO());
//        $dataGridState = DataGridState::createByParamFetcher($paramFetcher, new VehicleListDTO());
        $vehicles = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $vehiclesDTO = $this->get('tixi_api.assemblervehicle')->vehiclesToVehicleListDTOs($vehicles);
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($vehiclesDTO);
        $headers = $this->get('tixi_api.datagrid')->createHeaderArray(new VehicleListDTO());
        $partial = $request->get('partial');
        if(empty($partial) && !$partial) {
            $template = 'TixiApiBundle:Vehicle:list.html.twig';
        }else {
            $template = 'TixiApiBundle:Shared:datagrid.tablebody.html.twig';
        }
        return $this->render($template,array('rowIdPrefix'=>'vehicles', 'tableHeaders'=>$headers,'tableRows'=>$rows, 'totalAmountOfRows'=>$totalAmount));
    }

    /**
     * @Route("/{vehicleId}",requirements={"vehicleId" = "^(?!new)[^/]+$"},name="tixiapi_vehicle_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleId
     * @return mixed
     * @Breadcrumb("Fahrzeug {vehicleId}", route={"name"="tixiapi_vehicle_get", "parameters"={"vehicleId"}})
     */
    public function getVehicleAction(Request $request, $vehicleId) {
        $vehicle = $this->get('vehicle_repository')->find($vehicleId);
        $vehicleDTO = $this->get('tixi_api.assemblervehicle')->toVehicleListDTO($vehicle);
        $data = array('vehicle' => $vehicleDTO);
        return $this->render('TixiApiBundle:Vehicle:get.html.twig',array('vehicle'=>$vehicleDTO, 'serviceplansembedded'=>''));
    }

    /**
     * @Route("/new",name="tixiapi_vehicle_new")
     * @Method({"GET","POST"})
     *
     * @Breadcrumb("Neues Fahrzeug", route="tixiapi_vehicle_new")
     */
    public function newVehicleAction(Request $request) {
        $form = $this->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicles_get'));
        }
        return $this->render('TixiApiBundle:Vehicle:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{vehicleId}/editbasic",name="tixiapi_vehicle_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("Fahrzeug {vehicleId}", route={"name"="tixiapi_vehicle_editbasic", "parameters"={"vehicleId"}})
     */
    public function editVehicleAction(Request $request, $vehicleId) {
        $vehicleDTO = null;
        if($request->getMethod()==='GET') {
            $vehicle = $this->get('vehicle_repository')->find($vehicleId);
            if(null === $vehicle) {
                throw $this->createNotFoundException('The vehicle does not exist');
            }
            $vehicleDTO = $this->get('tixi_api.assemblervehicle')->toVehicleRegisterDTO($vehicle);
        }
        $form = $this->getForm(null,$vehicleDTO);
        $form->handleRequest($request);
        if($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicles_get',array('vehicleId'=>$vehicleId)));
        }
        return $this->render('TixiApiBundle:Vehicle:edit.html.twig', array('form'=>$form->createView(), 'vehicle'=>$vehicleDTO));
    }

    protected function registerOrUpdateVehicle(VehicleRegisterDTO $vehicleDTO) {
        if(is_null($vehicleDTO->id)) {
            $vehicle = $this->get('tixi_api.assemblervehicle')->registerDTOtoNewVehicle($vehicleDTO);
            $this->get('vehicle_repository')->store($vehicle);
        }else {
            $vehicle = $this->get('vehicle_repository')->find($vehicleDTO->id);
            $this->get('tixi_api.assemblervehicle')->registerDTOToVehicle($vehicle, $vehicleDTO);
        }
    }

    protected function getForm($targetRoute = null, $vehicleDTO = null, $parameters=array(), $method = 'POST') {
        if($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        }else {
            $options = array();
        }
        return $this->createForm(new VehicleType(), $vehicleDTO, $options);
    }



}