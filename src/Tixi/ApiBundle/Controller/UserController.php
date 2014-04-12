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
use Tixi\ApiBundle\Tile\Core\FormTile;
use Tixi\ApiBundle\Tile\Core\PanelSplitterTile;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\ApiBundle\Tile\User\UserRegisterFormViewTile;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class UserController
 * @package Tixi\ApiBundle\Controller
 * @Route("/users")
 * @Breadcrumb("user.panel.name", route="tixiapi_users_get")
 */
class UserController extends Controller {
    /**
     * @Route("", name="tixiapi_users_get")
     * @Method({"GET","POST"})
     */
    public function getUsersAction(Request $request, $embeddedState = false) {
        $embeddedParameter = (null === $request->get('embedded') || $request->get('embedded') === 'false') ? false : true;
        $isEmbedded = ($embeddedState || $embeddedParameter);
        $dataGridHandler = $this->get('tixi_api.datagridhandler');
        $dataGridControllerFactory = $this->get('tixi_api.datagridcontrollerfactory');
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $gridController = $dataGridControllerFactory->createUserController($isEmbedded);
        $dataGridTile = $dataGridHandler->createDataGridTileByRequest($request, $gridController);
        return new Response($tileRenderer->render($dataGridTile));
    }

    /**
     * @Route("/{userId}", requirements={"userId" = "^(?!new)[^/]+$"}, name="tixiapi_user_get")
     * @Method({"GET","POST"})
     * @Breadcrumb("{userId}", route={"name"="tixiapi_user_get", "parameters"={"userId"}})
     */
    public function getUserAction(Request $request, $userId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $user = $this->get('tixi_user_repository')->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('The user with id ' . $userId . ' does not exists');
        }
        $userDTO = $this->get('tixi_api.assembleruser')->userToUserRegisterDTO($user);
        $rootPanel = new RootPanel('tixiapi_users_get', $user->getUsername());
        $rootPanel->add(new UserRegisterFormViewTile('userRequest', $userDTO,
            $this->generateUrl('tixiapi_user_edit', array('userId' => $userId))));
        return new Response($tileRenderer->render($rootPanel));
    }

    /**
     * @Route("/new", name="tixiapi_user_new")
     * @Method({"GET","POST"})
     * @Breadcrumb("user.panel.new", route="tixiapi_user_new")
     */
    public function newUserAction(Request $request) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $form = $this->getForm();
        $form->handleRequest($request);
        $rootPanel = new RootPanel('tixiapi_users_get', 'user.panel.new');
        $rootPanel->add(new FormTile('userNewForm', $form, true));
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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @Breadcrumb("{userId}", route={"name"="tixiapi_user_edit", "parameters"={"userId"}})
     */
    public function editUserAction(Request $request, $userId) {
        $tileRenderer = $this->get('tixi_api.tilerenderer');
        $userAssembler = $this->get('tixi_api.assembleruser');
        $user = $this->get('tixi_user_repository')->find($userId);
        if (null === $user) {
            throw $this->createNotFoundException('This user does not exist');
        }
        $userDTO = $userAssembler->userToUserRegisterDTO($user);
        $form = $this->getForm(null, $userDTO);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $userDTO = $form->getData();
            $this->registerOrUpdateUser($userDTO);
            $this->get('entity_manager')->flush();
            return $this->redirect($this->generateUrl('tixiapi_user_get', array('userId' => $userId)));
        }
        $rootPanel = new RootPanel('tixiapi_users_get', 'user.panel.edit');
        $rootPanel->add(new FormTile('userEditForm', $form, true));
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
    protected function getForm($targetRoute = null, $userDTO = null, $parameters = array(), $method = 'POST') {
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

    public function assignNormalUserRole(User $user){
        $user->assignRole($this->getUserRole('ROLE_USER'));
    }

    public function getUserRole($roleName) {
        $role = $this->get('tixi_role_repository')->findOneBy(array('role' => $roleName));
        if (empty($role)) {
            throw $this->createNotFoundException('This role does not exist');
        }
        return $role;
    }

    public function isUsernameAvailable($username) {
        $duplicate = $this->get('tixi_user_repository')->findOneBy(array('username' => $username));
        if (!empty($duplicate)) {
            return false;
        }
        return true;
    }

    protected function encodeUserPassword(User $user){
        $encFactory = $this->get('security.encoder_factory');
        $encoder = $encFactory->getEncoder($user);
        $encPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($encPassword);
    }
}