<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:21
 */

namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\InsuranceType;
use Tixi\ApiBundle\Interfaces\Management\InsuranceAssembler;
use Tixi\ApiBundle\Interfaces\Management\InsuranceRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\ReferentialConstraintErrorTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;

/**
 * Class InsuranceController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("insurance.breadcrumb.name", route="tixiapi_management_insurances_get")
 * @Route("/management/insurances")
 */
class InsuranceController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementInsuranceId;
    }

    /**
     * @Route("",name="tixiapi_management_insurances_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getInsurancesAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementInsuranceController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'insurance.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{insuranceId}/delete",name="tixiapi_management_insurance_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $insuranceId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteInsuranceAction(Request $request, $insuranceId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $insurance = $this->getInsurance($insuranceId);
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $passengerRepository = $this->get('passenger_repository');
        $usageAmount = $passengerRepository->getAmountByInsurance($insurance);
        if ($usageAmount > 0) {
            $rootPanel = new RootPanel($this->menuId, 'error.refintegrity.header.name');
            $rootPanel->add(new ReferentialConstraintErrorTile($usageAmount));
            return new Response($tileRenderer->render($rootPanel));
        } else {
            $insurance->deleteLogically();
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_insurances_get'));
        }
    }

    /**
     * @Route("/new",name="tixiapi_management_insurance_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("insurance.panel.new", route="tixiapi_management_insurance_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newInsuranceAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $insuranceDTO = $form->getData();
            $this->registerOrUpdateInsurance($insuranceDTO);
            try {
                $this->get('entity_manager')->flush();
            } catch (DBALException $e) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_insurances_get'));
            }
        }

        $rootPanel = new RootPanel($this->menuId, 'insurance.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{insuranceId}/edit", name="tixiapi_management_insurance_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{insuranceId}", route={"name"="tixiapi_management_insurance_edit", "parameters"={"insuranceId"}})
     * @param Request $request
     * @param $insuranceId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editInsuranceAction(Request $request, $insuranceId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        /** @var InsuranceAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerinsurance');

        $insurance = $this->getInsurance($insuranceId);
        $insuranceDTO = $assembler->toInsuranceRegisterDTO($insurance);
        $form = $this->getForm($insuranceDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $insuranceDTO = $form->getData();
            $this->registerOrUpdateInsurance($insuranceDTO);
            try {
                $this->get('entity_manager')->flush();
            } catch (DBALException $e) {
                $errorMsg = $this->get('translator')->trans('form.error.valid.unique');
                $error = new FormError($errorMsg);
                $form->addError($error);
                $form->get('name')->addError($error);
            }

            //if no errors/invalids in form
            if (count($form->getErrors()) < 1) {
                return $this->redirect($this->generateUrl('tixiapi_management_insurances_get'));
            }
        }
        $rootPanel = new RootPanel($this->menuId, 'insurance.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_insurance_delete', array('insuranceId' => $insuranceId)), 'insurance.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $insuranceDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    protected function getForm($insuranceDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new InsuranceType($this->menuId), $insuranceDTO, $options);
    }

    /**
     * @param InsuranceRegisterDTO $dto
     * @return mixed
     */
    protected function registerOrUpdateInsurance(InsuranceRegisterDTO $dto) {
        /** @var InsuranceAssembler $assembler */
        $assembler = $this->get('tixi_api.assemblerinsurance');
        $repository = $this->get('insurance_repository');
        if (null === $dto->id) {
            $insurance = $assembler->registerDTOtoNewInsurance($dto);
            $repository->store($insurance);
            return $insurance;
        } else {
            $insurance = $this->getInsurance($dto->id);
            $assembler->registerDTOtoInsurance($insurance, $dto);
            return $insurance;
        }
    }

    /**
     * @param $insuranceId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getInsurance($insuranceId) {
        $repository = $this->get('insurance_repository');
        $insurance = $repository->find($insuranceId);
        if (null === $insurance) {
            throw $this->createNotFoundException('The insurance with id ' . $insuranceId . ' does not exist');
        }
        return $insurance;
    }

} 