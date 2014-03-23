<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:04
 */

namespace Tixi\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tixi\ApiBundle\Form\ServicePlanType;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\ServicePlanAssembler;
use Tixi\ApiBundle\Interfaces\ServicePlanAssignDTO;
use Tixi\ApiBundle\Interfaces\ServicePlanEmbeddedListDTO;
use Tixi\ApiBundle\Interfaces\ServicePlanListDTO;
use Tixi\ApiBundle\Interfaces\VehicleAssembler;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use Tixi\CoreDomain\ServicePlan;
use Tixi\CoreDomain\Vehicle;


class ServicePlanController extends Controller {


    /**
     * @QueryParam(name="embedded")
     * @QueryParam(name="partial")
     * @QueryParam(name="page")
     * @QueryParam(name="limit")
     * @QueryParam(name="orderbyfield")
     * @QueryParam(name="orderbydirection")
     * @QueryParam(name="filterstr")
     */
    public function getServiceplansAction($vehicleId, Request $request, ParamFetcherInterface $paramFetcher, $embedded=false) {
        $viewHandler = $this->get('fos_rest.view_handler');
        $dataGridState = DataGridState::createByParamFetcher($paramFetcher, ServicePlanEmbeddedListDTO::createReferenceDTOByVehicleId($vehicleId));
        $servicePlans = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $servicePlansDTO = null;
        $partial = $paramFetcher->get('partial');
        $isEmbedded = $embedded || ($paramFetcher->get('embedded') !== null && $paramFetcher->get('embedded'));
        if($isEmbedded) {
            $servicePlansDTO = $this->get('tixi_api.assemblerserviceplan')->servicePlansToServicePlanEmbeddedListDTOs($servicePlans);
        }else {
            //there is no full list at the moment
        }
        $servicePlansDTO = null === $servicePlansDTO ? array() : $servicePlansDTO;
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($servicePlansDTO);
        $routeParameters = array($vehicleId);
        $view = View::create();
        if($viewHandler->isFormatTemplating($request->get('_format'))) {
            $headers = null;
            if($isEmbedded && !$partial) {
                $headers = $this->get('tixi_api.datagrid')->createHeaderArray(ServicePlanEmbeddedListDTO::createReferenceDTOByVehicleId($vehicleId));
                $view->setTemplate('TixiApiBundle:ServicePlan:embeddedlist.html.twig');
                $srcUrl = $this->get('router')->generate('get_vehicle_serviceplans', array('vehicleId' => $vehicleId));
                $view->setData(array('routeParameters'=>$routeParameters, 'datagrids'=>array(array('rowIdPrefix'=>'serviceplans','dataSrcUrl'=>$srcUrl,'tableHeaders'=>$headers,'tableRows'=>$rows, 'totalAmountOfRows'=>$totalAmount))));
            }else {
                if(empty($partial) && !$partial) {
                    //there is no full list at the moment
                }else {
                    $view->setTemplate('TixiApiBundle:Shared:datagrid.tablebody.html.twig');
                    $view->setData(array('rowIdPrefix'=>'serviceplans','tableHeaders'=>$headers,'tableRows'=>$rows, 'totalAmountOfRows'=>$totalAmount));
                }
            }
        }else {
            //no json/xml at the moment
        }
        return $viewHandler->handle($view);
    }

    public function getServiceplanAction(Request $request, $vehicleId, $servicePlanId) {
        $viewHandler = $this->get('fos_rest.view_handler');
        if($viewHandler->isFormatTemplating($request->get('_format'))) {
            $view = View::createRouteRedirect('edit_vehicle_serviceplan',array('vehicleId'=>$vehicleId,'servicePlanId'=>$servicePlanId));
        }else {
            //ToDo define json resonse
        }
        return $viewHandler->handle($view);
    }


    public function newServiceplanAction($vehicleId) {
        if(!$this->vehicleExists($vehicleId)){throw new NotFoundHttpException();}
        $form = $this->getForm('post_vehicle_serviceplans');
        $form->get('vehicleId')->setData($vehicleId);
        $view = View::create($form);
        $view->setTemplate('TixiApiBundle:ServicePlan:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function editServiceplanAction($vehicleId, $servicePlanId) {
        if(!$this->vehicleExists($vehicleId)){throw new NotFoundHttpException();}
        $servicePlan = $this->get('serviceplan_repository')->find($servicePlanId);
        if(is_null($servicePlan)) {
            throw new NotFoundHttpException();
        }
        $servicePlanDTO = $this->get('tixi_api.assemblerserviceplan')->toServicePlanAssignDTO($servicePlan);
        $data = $this->getForm('post_vehicle_serviceplans', $servicePlanDTO);
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:ServicePlan:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);

    }

    public function postServiceplansAction(Request $request) {
        $form = $this->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $servicePlanAssignDTO = $form->getData();
            $vehicle = $this->get('vehicle_repository')->find($servicePlanAssignDTO->vehicleId);
            $this->registerOrUpdateServicePlanToVehicle($servicePlanAssignDTO, $vehicle);
            $this->getDoctrine()->getManager()->flush();
            $view = View::createRouteRedirect('get_vehicle',array('vehicleId'=>$servicePlanAssignDTO->vehicleId));
        }else {
            $view = View::create($form);
            $view->setTemplate('TixiApiBundle:ServicePlan:new.html.twig');
        }
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    protected function registerOrUpdateServicePlanToVehicle(ServicePlanAssignDTO $servicePlanAssignDTO, Vehicle $vehicle) {
        if(is_null($servicePlanAssignDTO->id)) {
            $servicePlan = ServicePlan::registerServicePlan($servicePlanAssignDTO->startDate, $servicePlanAssignDTO->endDate);
            $vehicle->assignServicePlan($servicePlan);
            $this->get('serviceplan_repository')->store($servicePlan);
        }else {
            $servicePlan = $this->get('serviceplan_repository')->find($servicePlanAssignDTO->id);
            $servicePlan->updateBasicData($servicePlanAssignDTO->startDate, $servicePlanAssignDTO->endDate);
        }
    }

    protected function getForm($targetRoute = null, $servicePlanDTO = null, $parameters=array(), $method = 'POST') {
        $options = array();
        if($targetRoute) {
            $options['action'] = $this->generateUrl($targetRoute, $parameters);
            $options['method'] = $method;
        }
        return $this->createForm(new ServicePlanType(), $servicePlanDTO, $options);
    }

    protected function vehicleExists($vehicleId) {
        return !is_null($this->get('vehicle_repository')->find($vehicleId));
    }


} 