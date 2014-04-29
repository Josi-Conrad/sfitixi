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
use Tixi\ApiBundle\Form\Management\ZonePlanType;
use Tixi\ApiBundle\Interfaces\Management\ZonePlanDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\Dispo\ZonePlan;

/**
 * Class ZonePlanController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("zoneplan.panel.name")
 * @Route("/management/zoneplan")
 */
class ZonePlanController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementZonePlanId;
    }

    /**
     * @Route("/edit", name="tixiapi_management_zoneplan_edit")
     * @Method({"GET","POST"})
     * @param Request $request
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editZonePlanAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $zonePlanAssembler = $this->get('tixi_api.assemblerZonePlan');
        $zonePlanManagement = $this->get('tixi_app.zoneplanmanagement');
        $zonePlan = $zonePlanManagement->getZonePlan();

        if (null === $zonePlan) {
            throw $this->createNotFoundException('Could not get zonePlan');
        }
        $zonePlanDTO = $zonePlanAssembler->zonePlanToDTO($zonePlan);
        $form = $this->getForm($zonePlanDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $zonePlanDTO = $form->getData();
            $zonePlan = $zonePlanAssembler->dtoToZonePlan($zonePlanDTO);
            $zonePlanManagement->createOrUpdateZonePlan($zonePlan);
            return $this->redirect($this->generateUrl('tixiapi_management_zoneplan_edit'));
        }
        $rootPanel = new RootPanel($this->menuId, 'zoneplan.panel.edit');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $zonePlanDTO
     * @param null $targetRoute
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($zonePlanDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new ZonePlanType($this->menuId), $zonePlanDTO, $options);
    }
}