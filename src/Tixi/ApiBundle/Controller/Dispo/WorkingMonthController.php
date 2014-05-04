<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 04.05.14
 * Time: 01:07
 */

namespace Tixi\ApiBundle\Controller\Dispo;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Form\Dispo\WorkingMonthNewType;
use Tixi\ApiBundle\Form\Dispo\WorkingMonthType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\WorkingMonthTile;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;

/**
 * Class WorkingMonthController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/disposition/workingmonth")
 * @Breadcrumb("workingmonth.panel.name", route="tixiapi_dispo_workingmonths_get")
 */
class WorkingMonthController extends Controller {
    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuWorkingMonthId;
    }

    /**
     * @Route("",name="tixiapi_dispo_workingmonths_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getWorkingMonthsAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoWorkingMonthController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'workingmonth.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{workingMonthId}/edit", name="tixiapi_dispo_workingmonth_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("workingmonth.panel.edit")
     * @param Request $request
     * @param $workingMonthId
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return RedirectResponse|Response
     */
    public function editWorkingMonthAction(Request $request, $workingMonthId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $workingMonthRepository = $this->get('workingmonth_repository');
        $workingDayRepository = $this->get('workingday_repository');
        $workingMonthAssembler = $this->get('tixi_api.assemblerWorkingMonth');
        $shiftRepository = $this->get('shift_repository');

        $workingMonth = $workingMonthRepository->find($workingMonthId);
        if (null === $workingMonth) {
            throw $this->createNotFoundException('This workingMonth does not exist');
        }
        $workingMonthDTO = $workingMonthAssembler->workingMonthToDTO($workingMonth);
        $form = $this->createForm(new WorkingMonthType($this->menuId), $workingMonthDTO);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $workingMonthDTO = $form->getData();
            $workingMonthAssembler->dtoToWorkingMonth($workingMonthDTO, $workingMonth,
                $workingMonthRepository, $workingDayRepository, $shiftRepository);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_dispo_workingmonths_get'));
        }

        $rootPanel = new RootPanel($this->menuId, 'workingmonth.panel.edit');
        $rootPanel->add(new WorkingMonthTile($form));

        return new Response($tileRenderer->render($rootPanel));
    }


    /**
     * @Route("/new",name="tixiapi_dispo_workingmonth_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("workingmonth.panel.new", route="tixiapi_dispo_workingmonth_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newWorkingMonthAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->createForm(new WorkingMonthNewType($this->menuId));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $workingMonthNewDTO = $form->getData();
            $workingMonth = $this->createWorkingMonthFromDate(
                '2014-07',
                $workingMonthNewDTO->workingMonthMemo);
            return $this->redirect($this->generateUrl('tixiapi_dispo_workingmonth_edit', array('workingMonthId' => $workingMonth->getId())));
        }
        $rootPanel = new RootPanel($this->menuId, 'workingmonth.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param $year_month
     * @param $memo
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return mixed|null|\Tixi\CoreDomain\Dispo\WorkingMonth
     */
    private function createWorkingMonthFromDate($year_month, $memo) {
        $workingMonthRepository = $this->get('workingmonth_repository');
        $workingDayRepository = $this->get('workingday_repository');
        $shiftRepository = $this->get('shift_repository');
        $shiftTypeRepository = $this->get('shifttype_repository');

        $workingMonth = null;

        try {
            $dateMonth = new \DateTime($year_month);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Problems with date:  ' . $year_month . '.
            Message:' . $e->getMessage());
        }
        try {
            $workingMonth = $workingMonthRepository->findWorkingMonthByDate($dateMonth);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Problems to find/create workingMonth with date:  ' . $year_month . '.
            Message:' . $e->getMessage());
        }

        if ($workingMonth === null) {
            $workingMonth = WorkingMonth::registerWorkingMonth($dateMonth);
            $workingMonth->setMemo($memo);
            $workingMonth->createWorkingDaysForThisMonth();

            $shiftTypes = $shiftTypeRepository->findAllNotDeleted();

            /**@var $workingDay WorkingDay */
            foreach ($workingMonth->getWorkingDays() as $workingDay) {
                $workingDayRepository->store($workingDay);
                foreach ($shiftTypes as $shiftType) {
                    $shift = Shift::registerShift($workingDay, $shiftType);
                    $workingDay->assignShift($shift);
                    $shiftRepository->store($shift);
                }
            }

            $workingMonthRepository->store($workingMonth);
            $this->get('entity_manager')->flush();
        }
        return $workingMonth;
    }

} 