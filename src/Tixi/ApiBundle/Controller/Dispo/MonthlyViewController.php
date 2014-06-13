<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.05.14
 * Time: 22:09
 */

namespace Tixi\ApiBundle\Controller\Dispo;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\Dispo\MonthlyView\MonthlyPlanEditType;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanAssembler;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\MonthlyPlanEditTile;

/**
 * Class MonthlyViewController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/disposition/monthlyplan")
 * @Breadcrumb("monthlyplan.panel.name", route="tixiapi_dispo_monthlyplans_get")
 */
class MonthlyViewController extends Controller{

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuDispositionMonthlyPlanId;
    }

    /**
     * @Route("",name="tixiapi_dispo_monthlyplans_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMonthlyPlansAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoMonthlyPlanController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'monthlyplan.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/month/{workingMonthId}",name="tixiapi_dispo_monthlyplan_workingdays_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{workingMonthId}", route={"name"="tixiapi_dispo_monthlyplan_workingdays_get", "parameters"={"workingMonthId"}})
     * @param Request $request
     * @param bool $embeddedState
     * @param $workingMonthId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMonthlyPlanWorkingDaysAction(Request $request, $embeddedState = false, $workingMonthId) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoMonthlyPlanWorkingDayController($embeddedState, array('workingMonthId' => $workingMonthId));
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'monthlyplan.workingday.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/month/{workingMonthId}/day/{workingDayId}/edit",name="tixiapi_dispo_monthlyplan_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{workingMonthId}", route={"name"="tixiapi_dispo_monthlyplan_workingdays_get", "parameters"={"workingMonthId"}})
     * @Breadcrumb("monthlyplan.workingday.list.name", route={"name"="tixiapi_dispo_monthlyplan_edit", "parameters"={"workingMonthId","workingDayId"}})
     * @param Request $request
     * @param $workingMonthId
     * @param $workingDayId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editMonthlyPlanAction(Request $request, $workingMonthId, $workingDayId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var MonthlyPlanAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblermonthlyplan');

        $workingDay = $this->getWorkingDayById($workingDayId);
        $editDTO = $assembler->workingDayToEditDTO($workingDay, $workingMonthId);
        $form = $this->createForm(new MonthlyPlanEditType($this->menuId), $editDTO);

        $form->handleRequest($request);

        if($form->isValid()) {
            $editDTO = $form->getData();
            $assembler->editDTOtoWorkingDay($editDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_dispo_monthlyplan_edit',
                array('workingMonthId'=>$workingMonthId, 'workingDayId'=>$workingDayId)));
        }

        $rootPanel = new RootPanel($this->menuId, 'monthlyplan.panel.edit');
        $rootPanel->add(new MonthlyPlanEditTile($form));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * For future release, delete DrivingAssertion directly in MonthPlanView
     * @param Request $request
     * @param $workingMonthId
     * @param $workingDayId
     */
    public function deleteDrivingAssertion(Request $request, $workingMonthId, $workingDayId) {

    }

    /**
     * @param $workingDayId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getWorkingDayById($workingDayId) {
        $workingDayRepository = $this->get('workingday_repository');
        $workingDay = $workingDayRepository->find($workingDayId);
        if(null === $workingDay) {
            throw $this->createNotFoundException('The workingDay with id ' . $workingDayId . ' does not exist');
        }
        return $workingDay;
    }

} 