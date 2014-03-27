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
use Tixi\ApiBundle\Form\DriverType;
use Tixi\ApiBundle\Interfaces\DriverListDTO;
use Tixi\ApiBundle\Interfaces\DriverRegisterDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGrid;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class DriverController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Fahrer", route="tixiapi_drivers_get")
 * @Route("/drivers")
 */
class DriverController extends Controller {

    /**
     * @Route("",name="tixiapi_drivers_get")
     * @Method({"GET","POST"})
     * GetParameters:
     * page,limit,orderbyfield,orderbydirection
     * filterstr,partial,embedded
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDriversAction(Request $request) {
        $dataGridState = DataGridState::createByRequest($request, new DriverListDTO());
        $drivers = $this->get('tixi_coredomain.fgea_repository')->findByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $driversDTO = $this->get('tixi_api.assemblerdriver')->driversToDriverListDTOs($drivers);
        $totalAmount = $this->get('tixi_coredomain.fgea_repository')->findTotalAmountByFilter($this->get('tixi_api.datagrid')->createGenericEntityFilterByState($dataGridState));
        $rows = $this->get('tixi_api.datagrid')->createRowsArray($driversDTO);
        $headers = $this->get('tixi_api.datagrid')->createHeaderArray(new DriverListDTO());
        $partial = $request->get('partial');
        if (empty($partial)) {
            $template = 'TixiApiBundle:Driver:list.html.twig';
        } else {
            $template = 'TixiApiBundle:Shared:datagrid.tablebody.html.twig';
        }
        return $this->render($template, array('rowIdPrefix' => 'drivers', 'tableHeaders' => $headers, 'tableRows' => $rows, 'totalAmountOfRows' => $totalAmount));
    }

    /**
     * @Route("/{driverId}",requirements={"driverId" = "^(?!new)[^/]+$"},
     * name="tixiapi_driver_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $driverId
     * @return mixed
     * @Breadcrumb("Fahrer Details", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     */
    public function getDriverAction(Request $request, $driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        $driverDTO = $this->get('tixi_api.assemblerdriver')->toDriverRegisterDTO($driver);
        return $this->render('TixiApiBundle:Driver:get.html.twig',
            array('driver' => $driverDTO));
    }

    /**
     * @Route("/new",name="tixiapi_driver_new")
     * @Method({"GET","POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response
     * @Breadcrumb("Neuer Fahrer", route="tixiapi_driver_new")
     */
    public function newDriverAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $this->registerOrUpdateDriver($driverDTO);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('tixiapi_drivers_get'));
        }
        return $this->render('TixiApiBundle:Driver:new.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * @Route("/{driverId}/editbasic",name="tixiapi_driver_editbasic")
     * @Method({"GET","POST"})
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("Fahrer editieren", route={"name"="tixiapi_driver_editbasic", "parameters"={"driverId"}})
     */
    public function editDriverAction(Request $request, $driverId) {
        $driverDTO = null;
        if ($request->getMethod() === 'GET') {
            $driver = $this->get('driver_repository')->find($driverId);
            if (null === $driver) {
                throw $this->createNotFoundException('Driver does not exist');
            }
            $driverDTO = $this->get('tixi_api.assemblerdriver')->toDriverRegisterDTO($driver);
        }
        $form = $this->getForm(null, $driverDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $driverDTO = $form->getData();
            $this->registerOrUpdateDriver($driverDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_drivers_get', array('driverId' => $driverId)));
        }
        return $this->render('TixiApiBundle:Driver:edit.html.twig',
            array('form' => $form->createView(), 'driver' => $form->getData()));
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