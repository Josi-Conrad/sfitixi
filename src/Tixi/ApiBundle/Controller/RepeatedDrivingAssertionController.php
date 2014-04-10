<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 01:07
 */

namespace Tixi\ApiBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\RepeatedDrivingAssertionType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionRegisterDTO;
use Tixi\ApiBundle\Interfaces\Dispo\ShiftSelectionDTO;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\RepeatedAssertionTile;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedMonthlyDrivingAssertion;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionPlanRepositoryDoctrine;
use Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionRepositoryDoctrine;

/**
 * Class RepeatedDrivingAssertionController
 * @package Tixi\ApiBundle\Controller
 *
 * @Route("/drivers/{driverId}/repeatedassertionplan")
 * @Breadcrumb("driver.panel.name", route="tixiapi_drivers_get")
 */
class RepeatedDrivingAssertionController extends Controller{

    /**
     * @Route("", name="tixiapi_driver_repeatedassertionplans_get")
     * @Method({"GET","POST"})
     */
    public function getRepeatedAssertionPlansAction(Request $request, $driverId, $embeddedState = false) {
        $embeddedState = $embeddedState || ($request->get('embedded') !== null && $request->get('embedded'));

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createRepeatedDrivingAssertionPlanController($embeddedState, array('driverId' => $driverId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{assertionPlanId}/delete",name="tixiapi_driver_repeatedassertionplan_delete")
     * @Method({"GET","POST"})
     */
    public function deleteRepeatedAssertionPlanAction(Request $request, $driverId, $assertionPlanId) {

    }

    /**
     * @Route("/new", name="tixiapi_driver_repeatedassertionplan_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("repeateddrivingmission.panel.new")
     */
    public function newRepeatedAssertionPlanAction(Request $request, $driverId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $driver = $this->getDriver($driverId);
        $form = $this->createForm(new RepeatedDrivingAssertionType(), new RepeatedDrivingAssertionRegisterDTO());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $assertionFormDTO = $form->getData();
            $this->registerOrUpdateAssertionPlan($assertionFormDTO, $driver);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId'=>$driverId)));
        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'repeateddrivingmission.panel.new');
        $rootPanel->add(new RepeatedAssertionTile('monthlyAssertion',$form));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{assertionPlanId}/editbasic", name="tixiapi_driver_repeatedassertionplan_editbasic")
     * @Method({"GET","POST"})
     * @Breadcrumb("{driverId}", route={"name"="tixiapi_driver_get", "parameters"={"driverId"}})
     * @Breadcrumb("repeateddrivingmission.panel.edit")
     */
    public function editRepeatedAssertionPlanAction(Request $request, $driverId, $assertionPlanId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $assertionPlanRepository = $this->get('repeateddrivingassertionplan_repository');
        /** @var RepeatedDrivingAssertionAssembler $assembler*/
        $assembler = $this->get('tixi_api.repeateddrivingassertionplanassembler');

        /** @var RepeatedDrivingAssertionPlan $assertionPlan */
        $assertionPlan = $assertionPlanRepository->find($assertionPlanId);
        $driver = $this->getDriver($driverId);
        if(null === $assertionPlan) {
            throw $this->createNotFoundException('The assertion plan with id '+$assertionPlanId+' does not exist');
        }
        $assertionDTO = $assembler->toRepeatedRegisterDTO($assertionPlan);
        $form = $this->createForm(new RepeatedDrivingAssertionType(), $assertionDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $assertionDTO = $form->getData();
            $this->registerOrUpdateAssertionPlan($assertionDTO, $driver);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_driver_get', array('driverId'=>$driverId)));
        }

        $rootPanel = new RootPanel('tixiapi_drivers_get', 'repeateddrivingmission.panel.edit');
        $rootPanel->add(new RepeatedAssertionTile('monthlyAssertion',$form, $assertionPlan->getFrequency()));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param RepeatedDrivingAssertionRegisterDTO $assertionDTO
     * @param Driver $driver
     */
    protected function registerOrUpdateAssertionPlan(RepeatedDrivingAssertionRegisterDTO $assertionDTO, Driver $driver) {
        /** @var RepeatedDrivingAssertionPlanRepositoryDoctrine $assertionPlanRepository */
        $assertionPlanRepository = $this->get('repeateddrivingassertionplan_repository');
        /** @var RepeatedDrivingAssertionRepositoryDoctrine $assertionRepository*/
        $assertionRepository = $this->get('repeateddrivingassertion_repository');
        /** @var RepeatedDrivingAssertionAssembler $assembler*/
        $assembler = $this->get('tixi_api.repeateddrivingassertionplanassembler');

        /** @var RepeatedDrivingAssertionPlan $assertionPlan*/
        $assertionPlan = null;
        if (null === $assertionDTO->id) {
            $assertionPlan = $assembler->repeatedRegisterDTOToNewDrivingAssertionPlan($assertionDTO);
            $driver->assignRepeatedDrivingAssertionPlan($assertionPlan);
        } else {
            $assertionPlan = $assertionPlanRepository->find($assertionDTO->id);
            $assembler->repeatedRegisterDTOToDrivingAssertionPlan($assertionPlan, $assertionDTO);
            foreach($assertionPlan->getRepeatedDrivingAssertions() as $previousAssertions) {
                $assertionRepository->remove($previousAssertions);
            }
        }
        $repeatedAssertions = new ArrayCollection();
        if($assertionDTO->frequency === 'weekly') {
            $repeatedAssertions = $assembler->repeatedRegisterDTOtoWeeklyDrivingAssertions($assertionDTO);
        }else {
            $repeatedAssertions = $assembler->repeatedRegisterDTOtoMonthlyDrivingAssertions($assertionDTO);
        }
        /** @var RepeatedDrivingAssertion $repeatedAssertion */
        foreach($repeatedAssertions as $repeatedAssertion) {
            $repeatedAssertion->setAssertionPlan($assertionPlan);
            $assertionRepository->store($repeatedAssertion);
        }
        $assertionPlan->replaceRepeatedDrivingAssertions($repeatedAssertions);
        $assertionPlanRepository->store($assertionPlan);
    }

    protected function getAssertionPlan($assertionPlanId) {
        $assertionPlanRepository = $this->get('repeateddrivingassertionplan_repository');
        $assertionPlan = $assertionPlanRepository->find($assertionPlanId);
        if(null === $assertionPlan) {
            throw $this->createNotFoundException('The AssertionPlan with id ' . $assertionPlanId . ' does not exist');
        }
        return $assertionPlan;
    }

    protected function getDriver($driverId) {
        $driver = $this->get('driver_repository')->find($driverId);
        if(null === $driver) {
            throw $this->createNotFoundException('The driver with id ' . $driverId . ' does not exist');
        }
        return $driver;
    }

} 