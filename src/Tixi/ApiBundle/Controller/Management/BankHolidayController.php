<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 16.04.14
 * Time: 13:21
 */

namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\BankHolidayType;
use Tixi\ApiBundle\Interfaces\Management\BankHolidayRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\BankHoliday;

/**
 * Class BankHolidayController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("bankholiday.panel.name", route="tixiapi_management_bankholidays_get")
 * @Route("/management/bankholidays")
 */
class BankHolidayController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementBankHolidayId;
    }

    /**
     * @Route("",name="tixiapi_management_bankholidays_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getBankHolidayAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createManagementBankHolidayController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'bankholiday.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{bankHolidayId}/delete",name="tixiapi_management_bankholiday_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $bankHolidayId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteBankHolidayAction(Request $request, $bankHolidayId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $bankHoliday = $this->getBankHoliday($bankHolidayId);
        $bankHoliday->deleteLogically();
        $this->get('entity_manager')->flush();

        return $this->redirect($this->generateUrl('tixiapi_management_bankholidays_get'));
    }

    /**
     * @Route("/new",name="tixiapi_management_bankholiday_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("bankholiday.panel.new", route="tixiapi_management_bankholiday_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newBankHolidayAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $form = $this->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $bankHolidayDTO = $form->getData();
            $bankHoliday = $this->registerOrUpdateBankHoliday($bankHolidayDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_bankholidays_get', array('bankholidayId' => $bankHoliday->getId())));
        }

        $rootPanel = new RootPanel($this->menuId, 'bankholiday.panel.new');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{bankHolidayId}/edit", name="tixiapi_management_bankholiday_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{bankHolidayId}", route={"name"="tixiapi_management_bankholiday_edit", "parameters"={"bankHolidayId"}})
     * @param Request $request
     * @param $bankHolidayId
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editBankHolidayAction(Request $request, $bankHolidayId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $bankHolidayAssembler = $this->get('tixi_api.assemblerBankHoliday');
        $bankHoliday = $this->get('bankholiday_repository')->find($bankHolidayId);
        if (null === $bankHoliday) {
            throw $this->createNotFoundException('This bankHoliday does not exist');
        }
        $bankHolidayDTO = $bankHolidayAssembler->bankHolidayToBankHolidayRegisterDTO($bankHoliday);
        $form = $this->getForm($bankHolidayDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $bankHolidayDTO = $form->getData();
            $this->registerOrUpdateBankHoliday($bankHolidayDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_bankholidays_get', array('bankHolidayId' => $bankHolidayId)));
        }
        $rootPanel = new RootPanel($this->menuId, 'bankholiday.panel.edit');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param $bankHolidayId
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getBankHoliday($bankHolidayId) {
        $bankHolidayRepository = $this->get('bankholiday_repository');
        $bankHoliday = $bankHolidayRepository->find($bankHolidayId);
        if (null === $bankHoliday) {
            throw $this->createNotFoundException('The bankholiday with id ' . $bankHolidayId . ' does not exist');
        }
        return $bankHoliday;
    }

    /**
     * @param null $bankHolidayDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($bankHolidayDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new BankHolidayType($this->menuId), $bankHolidayDTO, $options);
    }

    /**
     * @param BankHolidayRegisterDTO $bankHolidayDTO
     * @return \Tixi\CoreDomain\BankHoliday
     */
    protected function registerOrUpdateBankHoliday(BankHolidayRegisterDTO $bankHolidayDTO) {
        $bankHolidayRepository = $this->get('bankholiday_repository');
        $bankHolidayAssembler = $this->get('tixi_api.assemblerbankholiday');
        if (empty($bankHolidayDTO->id)) {
            $bankHoliday = $bankHolidayAssembler->registerDTOtoNewBankHoliday($bankHolidayDTO);
            $bankHolidayRepository->store($bankHoliday);
            return $bankHoliday;
        } else {
            $bankHoliday = $bankHolidayRepository->find($bankHolidayDTO->id);
            $bankHolidayAssembler->registerDTOtoBankHoliday($bankHolidayDTO, $bankHoliday);
            return $bankHoliday;
        }
    }

} 