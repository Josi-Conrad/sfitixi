<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller\Management;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tixi\ApiBundle\Form\Management\UserProfileType;
use Tixi\ApiBundle\Interfaces\Management\UserProfileDTO;
use Tixi\ApiBundle\Interfaces\Management\UserRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\CustomFormView\UserRegisterFormViewTile;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class UserProfileController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Route("/user_profile_edit")
 */
class UserProfileController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuUserId;
    }

    /**
     * @Route("", name="tixiapi_user_profile_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @internal param bool $embeddedState
     * @return Response
     */
    public function editUserProfileAction(Request $request) {
        $user = $this->get('security.context')->getToken()->getUser();

        if (null === $user) {
            throw $this->createNotFoundException('This user does not exist');
        }

        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $userAssembler = $this->get('tixi_api.assembleruser');

        $userDTO = $userAssembler->userToUserProfileDTO($user);
        $form = $this->getForm($userDTO);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userDTO = $form->getData();
            $this->get('tixi_api.assembleruser')->registerProfileDTOtoUser($userDTO, $user);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixi_logout'));
        }
        $rootPanel = new RootPanel($this->menuId, $user->getUserName());
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param null $targetRoute
     * @param null $userDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($userDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new UserProfileType(), $userDTO, $options);
    }
}