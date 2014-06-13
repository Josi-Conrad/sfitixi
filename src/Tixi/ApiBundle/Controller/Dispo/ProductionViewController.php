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
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Dispo\ProductionView\ProductionPlanCreateType;
use Tixi\ApiBundle\Form\Dispo\ProductionView\ProductionPlanEditType;
use Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionPlanAssembler;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\Dispo\ProductionPlanEditTile;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

/**
 * Class DispositionProductionViewController
 * @package Tixi\ApiBundle\Controller\Dispo
 *
 * @Route("/disposition/productionplan")
 * @Breadcrumb("productionplan.panel.name", route="tixiapi_dispo_productionplans_get")
 */
class ProductionViewController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuDispositionProductionPlanId;
    }

    /**
     * @Route("",name="tixiapi_dispo_productionplans_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProductionPlansAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createDispoProductionPlanController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'productionplan.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{workingMonthId}/edit",name="tixiapi_dispo_productionplan_edit")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $workingMonthId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function editProductionPlanAction(Request $request, $workingMonthId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var ProductionPlanAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerproductionplan');

        $workingMonth = $this->getWorkingMonthById($workingMonthId);
        $editDTO = $assembler->workingMonthToEditDTO($workingMonth);
        $form = $this->createForm(new ProductionPlanEditType($this->menuId), $editDTO);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $editDTO = $form->getData();
            try {
                $assembler->editDtoToWorkingMonth($editDTO, $workingMonth);
                $this->get('entity_manager')->flush();
                return $this->redirect($this->generateUrl('tixiapi_dispo_productionplans_get'));
            } catch (\InvalidArgumentException $e) {
                $form->addError(new FormError($this->get('translator')->trans('productionplan.form.drivingpoolerror')));
            } catch (\LogicException $e) {
                $form->addError(new FormError($e->getMessage() . ': ' . $this->get('translator')->trans('productionplan.form.drivingpoolerror')));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'productionplan.panel.edit');
        $rootPanel->add(new ProductionPlanEditTile($form));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new",name="tixiapi_dispo_productionplan_new")
     * @Method({"GET","POST"})
     * @param Request $request
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newProductionPlanAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var ProductionPlanAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerproductionplan');
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->get('workingmonth_repository');

        $form = $this->createForm(new ProductionPlanCreateType($this->menuId));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $createDTO = $form->getData();
            /** @var WorkingMonth $workingMonth */
            $workingMonth = $assembler->createDTOtoNewProductionPlan($createDTO, $workingMonthRepository);
            if (null === $workingMonth) {
                throw $this->createNotFoundException('a problem occured during the creation process.');
            }
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_dispo_productionplan_edit', array('workingMonthId' => $workingMonth->getId())));
        }
        $rootPanel = new RootPanel($this->menuId, 'productionplan.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param $workingMonthId
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getWorkingMonthById($workingMonthId) {
        $workingMonthRepository = $this->get('workingmonth_repository');
        $workingMonth = $workingMonthRepository->find($workingMonthId);
        if (null === $workingMonth) {
            throw $this->createNotFoundException('The workingMonth with id ' . $workingMonthId . ' does not exist');
        }
        return $workingMonth;
    }


} 