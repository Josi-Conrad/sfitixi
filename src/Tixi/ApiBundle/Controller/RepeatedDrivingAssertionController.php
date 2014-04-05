<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 01:07
 */

namespace Tixi\ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\RepeatedMonthlyDrivingAssertionType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedMonthlyDrivingAssertionRegisterDTO;
use Tixi\ApiBundle\Interfaces\Dispo\ShiftSelectionDTO;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\RepeatedMonthlyAssertionTile;

/**
 * Class RepeatedDrivingAssertionController
 * @package Tixi\ApiBundle\Controller
 *
 * @Route("/drivers/{driverId}/assertions/repeated")
 */
class RepeatedDrivingAssertionController extends Controller{

    /**
     * @Route("/new", name="tixiapi_dispo_assertionMonthly")
     * @Method({"GET","POST"})
     */
    public function newMonthlyAssertionAction(Request $request, $driverId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $shiftTypeRepository = $this->get('shifttype_repository');
        $assertionPlanRepository = $this->get('repeateddrivingassertionplan_repository');

        $shifts = array();
        $shifts[] = $shiftTypeRepository->find(1);
        $shifts[] = $shiftTypeRepository->find(3);

        $shiftSelectionDTO = new ShiftSelectionDTO();
//        $shiftSelectionDTO->selectionId = 'First_Tuesday';
//        $shiftSelectionDTO->getShiftSelection()->add($shifts[0]);
//        $shiftSelectionDTO->getShiftSelection()->add($shifts[1]);

        $assertionDTO = new RepeatedMonthlyDrivingAssertionRegisterDTO();
//        $assertionDTO->getShiftSelections()->add($shiftSelectionDTO);

        $form = $this->createForm(new RepeatedMonthlyDrivingAssertionType(), $assertionDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $assertionFormDTO = $form->getData();
            $test = "";

        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'Dauereinsatz');
        $rootPanel->add(new RepeatedMonthlyAssertionTile('monthlyAssertion',$form));

        return new Response($tileRenderer->render($rootPanel));
    }

    protected function registerOrUpdateAssertionPlan(VehicleRegisterDTO $vehicleDTO) {
        if (is_null($vehicleDTO->id)) {
            $vehicle = $this->get('tixi_api.assemblervehicle')->registerDTOtoNewVehicle($vehicleDTO);
            $this->get('vehicle_repository')->store($vehicle);
        } else {
            $vehicle = $this->get('vehicle_repository')->find($vehicleDTO->id);
            $this->get('tixi_api.assemblervehicle')->registerDTOToVehicle($vehicle, $vehicleDTO);
        }
    }

} 