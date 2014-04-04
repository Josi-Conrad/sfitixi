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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tixi\ApiBundle\Form\ServicePlanType;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\ServicePlanAssembler;
use Tixi\ApiBundle\Interfaces\ServicePlanAssignDTO;
use Tixi\ApiBundle\Interfaces\ServicePlanEmbeddedListDTO;
use Tixi\ApiBundle\Interfaces\ServicePlanListDTO;
use Tixi\ApiBundle\Interfaces\VehicleAssembler;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridInputState;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\ServicePlan\ServicePlanRegisterFormViewTile;
use Tixi\CoreDomain\ServicePlan;
use Tixi\CoreDomain\Vehicle;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * Class ServicePlanController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrzeug", route="tixiapi_vehicles_get")
 * @Route("/vehicles/{vehicleId}/serviceplans")
 */
class ServicePlanController extends Controller {
    /**
     * @Route("",name="tixiapi_serviceplans_get")
     * @Method({"GET","POST"})
     */
    public function getServiceplansAction($vehicleId, Request $request, $embeddedState=false) {
        $embeddedState = $embeddedState || ($request->get('embedded') !== null && $request->get('embedded'));

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createServicePlanController($embeddedState, array('vehicleId'=>$vehicleId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{servicePlanId}",requirements={"servicePlanId" = "^(?!new)[^/]+$"},name="tixiapi_serviceplan_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $vehicleId
     * @param $servicePlanId
     * @return mixed
     */
    public function getServiceplanAction(Request $request, $vehicleId, $servicePlanId) {
        $servicePlanRepository = $this->get('serviceplan_repository');
        $assembler = $this->get('tixi_api.assemblerserviceplan');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $vehicle = $this->getVehicle($vehicleId);
        $servicePlan = $servicePlanRepository->find($servicePlanId);
        if(null === $servicePlan) {throw $this->createNotFoundException('The serviceplan with id '.$servicePlanId.' does not exists');}
        $servicePlanDTO = $assembler->toServicePlanAssignDTO($servicePlan);
        $rootPanel = new RootPanel('Serviceplan für ',$vehicle->getName());
        $rootPanel->add(new ServicePlanRegisterFormViewTile('servicePlanRequest', $servicePlanDTO, $this->generateUrl('tixiapi_serviceplan_editbasic', array('vehicleId'=>$vehicleId, 'servicePlanId'=>$servicePlanId))));

        $tileRenderer->render($rootPanel);
    }

    /**
     * @Route("/new", name="tixiapi_serviceplan_new")
     * @Method({"GET","POST"})
     * @param $vehicleId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function newServiceplanAction(Request $request, $vehicleId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $vehicle = $this->getVehicle($vehicleId);
        $form = $this->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $servicePlanAssignDTO = $form->getData();
            $this->registerOrUpdateServicePlanToVehicle($servicePlanAssignDTO, $vehicle);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>$vehicleId)));
        }
        $rootPanel = new RootPanel('tixiapi_vehicles_get', 'Neuer Serviceplan', ' für '.$vehicle->getName());
        $rootPanel->add(new FormTile('servicePlanNewForm',$form,true));
        return new Response($tileRenderer->render($rootPanel));
//
//        return $this->render('TixiApiBundle:ServicePlan:new.html.twig', array(
//            'form' => $form->createView()
//        ));
    }

    /**
     * @Route("/{servicePlanId}/editbasic", name="tixiapi_serviceplan_editbasic")
     * @Method({"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $vehicleId
     * @param $servicePlanId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return mixed
     */
    public function editServiceplanAction(Request $request, $vehicleId, $servicePlanId) {
        $vehicle = $this->getVehicle($vehicleId);
        $servicePlanAssignDTO = null;
        if($request->getMethod()==='GET') {
            $servicePlan = $this->get('serviceplan_repository')->find($servicePlanId);
            if(null === $servicePlan) {throw $this->createNotFoundException('The serviceplan does not exist');}
            $servicePlanAssignDTO = $this->get('tixi_api.assemblerserviceplan')->toServicePlanAssignDTO($servicePlan);
        }
        $form = $this->getForm(null,$servicePlanAssignDTO);
        $form->handleRequest($request);
        if($form->isValid()) {
            $servicePlanAssignDTO = $form->getData();
            $this->registerOrUpdateServicePlanToVehicle($servicePlanAssignDTO, $vehicle);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>$vehicleId)));
        }
        return $this->render('TixiApiBundle:ServicePlan:new.html.twig');
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

    protected function getVehicle($vehicleId) {
        $vehicle = $this->get('vehicle_repository')->find($vehicleId);
        if(null === $vehicle) {
            throw $this->createNotFoundException('The vehicle with id '.$vehicleId.' does not exists');
        }
        return $vehicle;
    }


} 