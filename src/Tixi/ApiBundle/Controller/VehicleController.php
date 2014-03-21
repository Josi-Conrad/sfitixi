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
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\ApiBundle\Interfaces\VehicleAssembler;
use Tixi\ApiBundle\Interfaces\VehicleRegisterDTO;
use Tixi\ApiBundle\Interfaces\VehicleListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use Tixi\CoreDomain\Vehicle;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class VehicleController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrzeuge", route="get_vehicles")
 */
class VehicleController extends Controller{

    /**
     * @QueryParam(name="page")
     * @QueryParam(name="limit")
     * @QueryParam(name="orderbyfield")
     * @QueryParam(name="orderbydirection")
     * @QueryParam(name="filterstr")
     * @QueryParam(name="partial")
     */
    public function getVehiclesAction(Request $request, ParamFetcherInterface $paramFetcher) {
        $viewHandler = $this->get('fos_rest.view_handler');
        $dataGridHandler = DataGridHandler::create(DataGridHandler::REPOSITORY_TYPE, $this->get('vehicle_repository'));
        $dataGridState = DataGridState::createByParamFetcher($paramFetcher, new VehicleListDTO());
        $vehicles = $dataGridHandler->findAllBy($dataGridState);
        $vehiclesDTO = $this->get('tixi_api.assemblervehicle')->vehiclesToVehicleListDTOs($vehicles);
        $totalAmount = $dataGridHandler->totalNumberOfRecords($dataGridState);
        $dataGrid = new DataGrid($this->get('annotation_reader'), new VehicleListDTO());
        $rows = $dataGrid->createRowsArray($vehiclesDTO);
        $view = View::create();
        if($viewHandler->isFormatTemplating($request->get('_format'))) {
            $headers = $dataGrid->createHeaderArray(new VehicleListDTO());
            $partial = $paramFetcher->get('partial');
            if(empty($partial) && !$partial) {
                $view->setTemplate('TixiApiBundle:Vehicle:list.html.twig');
            }else {
                $view->setTemplate('TixiApiBundle:Shared:datagrid.tablebody.html.twig');
            }

            $view->setData(array('rowIdPrefix'=>'vehicles', 'tableHeaders'=>$headers,'tableRows'=>$rows, 'totalAmountOfRows'=>$totalAmount));
        }else {

        }
        return $viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @param $vehicleId
     * @return mixed
     * @Breadcrumb("Fahrzeug {vehicleId}", route={"name"="get_vehicle", "parameters"={"vehicleId"}})
     */
    public function getVehicleAction(Request $request, $vehicleId) {
        $vehicle = $this->get('vehicle_repository')->find($vehicleId);
        $vehicleDTO = VehicleAssembler::toVehicleRegisterDTO($vehicle);
        $data = array('vehicle' => $vehicleDTO);

        $viewHandler = $this->get('fos_rest.view_handler');
        $view = View::create($data);

        if($viewHandler->isFormatTemplating($request->get('_format'))) {
            $view->setTemplate('TixiApiBundle:Vehicle:detail.html.twig');
        }
        return $viewHandler->handle($view);
    }

    public function newVehicleAction() {
        $data = $this->getForm('post_vehicles');
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Vehicle:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function editVehicleAction($vehicleId) {
        $vehicle = $this->get('vehicle_repository')->find($vehicleId);
        if(is_null($vehicle)) {
            throw new NotFoundHttpException();
        }
        $vehicleDTO = VehicleAssembler::toVehicleRegisterDTO($vehicle);
        $data = $this->getForm('post_vehicles',$vehicleDTO);
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Vehicle:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function postVehiclesAction(Request $request) {
        $form = $this->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $vehicleDTO = $form->getData();
            $this->registerOrUpdateVehicle($vehicleDTO);
            $this->getDoctrine()->getManager()->flush();
            $view = View::createRouteRedirect('get_vehicles');
        }else {
            $view = View::create($form);
            $view->setTemplate('TixiApiBundle:Vehicle:new.html.twig');
        }
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    protected function registerOrUpdateVehicle(VehicleRegisterDTO $vehicleDTO) {
        if(is_null($vehicleDTO->id)) {
            $vehicle = Vehicle::registerVehicle($vehicleDTO->name, $vehicleDTO->licenceNumber,
                $vehicleDTO->dateOfFirstRegistration, $vehicleDTO->parkingLotNumber, $vehicleDTO->vehicleCategory);
            $this->get('vehicle_repository')->store($vehicle);
        }else {
            $vehicle = $this->get('vehicle_repository')->find($vehicleDTO->id);
            $vehicle->updateBasicData($vehicleDTO->name, $vehicleDTO->licenceNumber,
                $vehicleDTO->dateOfFirstRegistration, $vehicleDTO->parkingLotNumber, $vehicleDTO->vehicleCategory);
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