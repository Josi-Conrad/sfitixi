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
use Tixi\ApiBundle\Form\DriverType;
use Tixi\ApiBundle\Interfaces\DriverListDTO;
use Tixi\ApiBundle\Interfaces\DriverRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use Tixi\CoreDomain\Vehicle;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class DriverController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrer", route="get_drivers")
 */
class DriverController extends Controller {

    /**
     * @QueryParam(name="page")
     * @QueryParam(name="limit")
     * @QueryParam(name="orderbyfield")
     * @QueryParam(name="orderbydirection")
     * @QueryParam(name="filterstr")
     * @QueryParam(name="partial")
     */
    public function getDriversAction(Request $request, ParamFetcherInterface $paramFetcher) {
        $viewHandler = $this->get('fos_rest.view_handler');
        $dataGridState = DataGridState::createByParamFetcher($paramFetcher, new DriverListDTO());
        $drivers = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $driversDTO = $this->get('tixi_api.assemblerdriver')->driversToDriverListDTOs($drivers);
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($driversDTO);
        $view = View::create();
        if ($viewHandler->isFormatTemplating($request->get('_format'))) {
            $headers = $this->get('tixi_api.datagrid')->createHeaderArray(new DriverListDTO());
            $partial = $paramFetcher->get('partial');
            if (empty($partial)) {
                $view->setTemplate('TixiApiBundle:Driver:list.html.twig');
            } else {
                $view->setTemplate('TixiApiBundle:Shared:datagrid.tablebody.html.twig');
            }
            $view->setData(array('rowIdPrefix' => 'drivers', 'tableHeaders' => $headers, 'tableRows' => $rows, 'totalAmountOfRows' => $totalAmount));
        } else {

        }
        return $viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @param $driverId
     * @return mixed
     * @Breadcrumb("Driver {driverId}", route={"name"="get_driver", "parameters"={"driverId"}})
     */
    public function getDriverAction(Request $request, $driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        $driverDTO = $this->get('tixi_api.assemblerdriver')->toDriverRegisterDTO($driver);
        $data = array('driver' => $driverDTO);
        $viewHandler = $this->get('fos_rest.view_handler');
        $view = View::create($data);
        if ($viewHandler->isFormatTemplating($request->get('_format'))) {
            $view->setTemplate('TixiApiBundle:Driver:detail.html.twig');
        }
        return $viewHandler->handle($view);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newDriverAction() {
        $data = $this->getForm('post_drivers');
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Driver:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param $driverId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editDriverAction($driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        if (empty($driver)) {
            throw new NotFoundHttpException();
        }
        $driverDTO = $this->get('tixi_api.assemblerdriver')->toDriverRegisterDTO($driver);
        $data = $this->getForm('post_drivers', $driverDTO);
        $view = View::create($data);
        $view->setTemplate('TixiApiBundle:Driver:new.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postDriversAction(Request $request) {
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $this->registerOrUpdateDriver($driverDTO);
            $this->get('entity_manager')->flush();
            $view = View::createRouteRedirect('get_drivers');
        } else { //not valid, show errors
            $view = View::create($form);
            $view->setTemplate('TixiApiBundle:Vehicle:new.html.twig');
        }
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param DriverRegisterDTO $driverDTO
     */
    protected function registerOrUpdateDriver(DriverRegisterDTO $driverDTO) {
        if (empty($driverDTO->id)) {
            $driver = $this->get('tixi_api.assemblerdriver')->registerDTOtoNewDriver($driverDTO);
            $this->get('address_repository')->store($driver->getAddress());
            $this->get('driver_repository')->store($driver);
        } else {
            $driver = $this->get('driver_repository')->find($driverDTO->id);
            $this->get('tixi_api.assemblerdriver')->registerDTOtoDriver($driver, $driverDTO);
        }
    }

    /**
     * @param null $targetRoute
     * @param null $driverDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($targetRoute = null, $driverDTO = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new DriverType(), $driverDTO, $options);
    }


}