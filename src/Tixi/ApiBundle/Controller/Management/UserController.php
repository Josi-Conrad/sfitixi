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
use Tixi\ApiBundle\Form\Management\UserEditType;
use Tixi\ApiBundle\Form\Management\UserRegisterType;
use Tixi\ApiBundle\Interfaces\Management\UserEditDTO;
use Tixi\ApiBundle\Interfaces\Management\UserRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelDeleteFooterTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class UserController
 * @package Tixi\ApiBundle\Controller\Management
 * @Breadcrumb("management.breadcrumb.name")
 * @Breadcrumb("user.panel.name", route="tixiapi_management_users_get")
 * @Route("/management/users")
 */
class UserController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuManagementUserId;
    }

    /**
     * @Route("", name="tixiapi_management_users_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @throws AccessDeniedException
     * @return Response
     */
    public function getUsersAction(Request $request, $embeddedState = false) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createUserController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if (!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'user.list.name');
            $rootPanel->add($dataGridTile);
        } else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_management_user_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("user.panel.new", route="tixiapi_management_user_new")
     * @param Request $request
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newUserAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $form = $this->createForm(new UserRegisterType());
        $form->handleRequest($request);
        $rootPanel = new RootPanel($this->menuId, 'user.panel.new');
        $rootPanel->add(new FormTile($form, true));
        if ($form->isValid()) {
            $userDTO = $form->getData();
            if (!$this->isUsernameAvailable($userDTO->username)) {
                return new Response($tileRenderer->render($rootPanel));
            }
            $user = $this->registerUser($userDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_users_get', array('userId' => $user->getId())));
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{userId}/edit", name="tixiapi_management_user_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{userId}", route={"name"="tixiapi_management_user_edit", "parameters"={"userId"}})
     * @param Request $request
     * @param $userId
     * @throws AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editUserAction(Request $request, $userId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $userAssembler = $this->get('tixi_api.assembleruser');
        $user = $this->get('tixi_user_repository')->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $userDTO = $userAssembler->userToUserEditDTO($user);
        $form = $this->getEditForm($userDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $userDTO = $form->getData();
            $this->updateUser($userDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_management_users_get', array('userId' => $userId)));
        }
        $rootPanel = new RootPanel($this->menuId, 'user.panel.edit');
        $rootPanel->add(new FormTile($form, true));
        $rootPanel->add(new PanelDeleteFooterTile($this->generateUrl('tixiapi_management_user_delete',
            array('userId' => $userId)), 'user.button.delete'));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{userId}/delete",name="tixiapi_management_user_delete")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param $userId
     * @throws AccessDeniedException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction(Request $request, $userId) {
        if (false === $this->get('security.context')->isGranted('ROLE_MANAGER')) {
            throw new AccessDeniedException();
        }
        $user = $this->getUserById($userId);
        $user->deleteLogically();
        $this->get('entity_manager')->flush();
        return $this->redirect($this->generateUrl('tixiapi_management_users_get'));
    }

    /**
     * @param UserRegisterDTO $userDTO
     * @return User
     */
    protected function registerUser(UserRegisterDTO $userDTO) {
        $user = $this->get('tixi_api.assembleruser')->registerDTOtoNewUser($userDTO, $this->get('tixi_role_repository'));
        $this->get('tixi_user_repository')->store($user);
        return $user;
    }

    /**
     * @param UserEditDTO $userDTO
     * @return null|object
     */
    protected function updateUser(UserEditDTO $userDTO) {
        $user = $this->get('tixi_user_repository')->find($userDTO->id);
        $this->get('tixi_api.assembleruser')->registerEditDTOtoUser($userDTO, $user, $this->get('tixi_role_repository'));
        return $user;
    }

    /**
     * @param null $targetRoute
     * @param null $userDTO
     * @param array $parameters
     * @param string $method
     * @return \Symfony\Component\Form\Form
     */
    protected function getEditForm($userDTO = null, $targetRoute = null, $parameters = array(), $method = 'POST') {
        if ($targetRoute) {
            $options = array(
                'action' => $this->generateUrl($targetRoute, $parameters),
                'method' => $method
            );
        } else {
            $options = array();
        }
        return $this->createForm(new UserEditType(), $userDTO, $options);
    }

    /**
     * @param $roleName
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getUserRole($roleName) {
        $role = $this->get('tixi_role_repository')->findOneBy(array('role' => $roleName));
        if (empty($role)) {
            throw $this->createNotFoundException('This role does not exist');
        }
        return $role;
    }

    /**
     * @param $username
     * @return bool
     */
    protected function isUsernameAvailable($username) {
        $duplicate = $this->get('tixi_user_repository')->findOneBy(array('username' => $username));
        if (!empty($duplicate)) {
            return false;
        }
        return true;
    }

    protected function getUserById($userId) {
        $user = $this->get('tixi_user_repository')->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('The user with id ' . $userId . ' does not exist');
        }
        return $user;
    }
}