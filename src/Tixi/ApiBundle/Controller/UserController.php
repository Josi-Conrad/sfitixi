<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\ApiBundle\Form\UserType;
use Tixi\ApiBundle\Interfaces\UserRegisterDTO;
use Tixi\ApiBundle\Menu\MenuService;
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\CustomFormView\UserRegisterFormViewTile;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class UserController
 * @package Tixi\ApiBundle\Controller
 * @Route("/users")
 * @Breadcrumb("user.panel.name", route="tixiapi_users_get")
 */
class UserController extends Controller {

    protected $menuId;

    public function __construct() {
        $this->menuId = MenuService::$menuUserId;
    }

    /**
     * @Route("", name="tixiapi_users_get")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param bool $embeddedState
     * @return Response
     */
    public function getUsersAction(Request $request, $embeddedState = false) {
        $embeddedState = $embeddedState || $request->get('embedded') === "true";
        $isPartial = $request->get('partial') === "true";

        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');

        $gridController = $dataGridControllerFactory->createUserController($embeddedState);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $this->menuId, $gridController);

        $rootPanel = null;
        if(!$embeddedState && !$isPartial) {
            $rootPanel = new RootPanel($this->menuId, 'user.list.name');
            $rootPanel->add($dataGridTile);
        }else {
            $rootPanel = $dataGridTile;
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{userId}", requirements={"userId" = "^(?!new)[^/]+$"}, name="tixiapi_user_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{userId}", route={"name"="tixiapi_user_get", "parameters"={"userId"}})
     * @param Request $request
     * @param $userId
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getUserAction(Request $request, $userId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $userRepository = $this->get('tixi_user_repository');

        $user = $userRepository->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('The user with id ' . $userId . ' does not exists');
        }
        $userDTO = $this->get('tixi_api.assembleruser')->userToUserRegisterDTO($user);
        $rootPanel = new RootPanel($this->menuId, $user->getUsername());
        $rootPanel->add(new UserRegisterFormViewTile('userRequest', $userDTO,
            $this->generateUrl('tixiapi_user_edit', array('userId' => $userId))));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_user_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("user.panel.new", route="tixiapi_user_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newUserAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $form = $this->getForm();
        $form->handleRequest($request);
        $rootPanel = new RootPanel($this->menuId, 'user.panel.new');
        $rootPanel->add(new FormTile($form, true));
        if ($form->isValid()) {
            $userDTO = $form->getData();
            if(!$this->isUsernameAvailable($userDTO->username)){
                return new Response($tileRenderer->render($rootPanel));
            }
            $user = $this->registerOrUpdateUser($userDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_user_get', array('userId' => $user->getId())));
        }

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/{userId}/edit", name="tixiapi_user_edit")
     * @Method({"GET","POST"})
     * @Breadcrumb("{userId}", route={"name"="tixiapi_user_edit", "parameters"={"userId"}})
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editUserAction(Request $request, $userId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $userAssembler = $this->get('tixi_api.assembleruser');
        $user = $this->get('tixi_user_repository')->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $userDTO = $userAssembler->userToUserRegisterDTO($user);
        $form = $this->getForm($userDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $userDTO = $form->getData();
            $this->registerOrUpdateUser($userDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_user_get', array('userId' => $userId)));
        }
        $rootPanel = new RootPanel($this->menuId, 'user.panel.edit');
        $rootPanel->add(new FormTile($form, true));

        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @param UserRegisterDTO $userDTO
     * @return null|object|\Tixi\SecurityBundle\Entity\User
     */
    protected function registerOrUpdateUser(UserRegisterDTO $userDTO) {
        if (empty($userDTO->id)) {
            $user = $this->get('tixi_api.assembleruser')->registerDTOtoNewUser($userDTO);
            $this->encodeUserPassword($user);
            $this->assignNormalUserRole($user);
            $this->get('tixi_user_repository')->store($user);
            return $user;
        } else {
            $user = $this->get('tixi_user_repository')->find($userDTO->id);
            $this->get('tixi_api.assembleruser')->registerDTOtoUser($userDTO, $user);
            $this->encodeUserPassword($user);
            return $user;
        }
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
        return $this->createForm(new UserType(), $userDTO, $options);
    }

    /**
     * @param User $user
     */
    public function assignNormalUserRole(User $user){
        $user->assignRole($this->getUserRole('ROLE_USER'));
    }

    /**
     * @param $roleName
     * @return null|object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getUserRole($roleName) {
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
    public function isUsernameAvailable($username) {
        $duplicate = $this->get('tixi_user_repository')->findOneBy(array('username' => $username));
        if (!empty($duplicate)) {
            return false;
        }
        return true;
    }

    /**
     * @param User $user
     */
    protected function encodeUserPassword(User $user){
        $encFactory = $this->get('security.encoder_factory');
        $encoder = $encFactory->getEncoder($user);
        $encPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($encPassword);
    }
}