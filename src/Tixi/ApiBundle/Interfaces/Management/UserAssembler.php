<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\SecurityContext;
use Tixi\SecurityBundle\Entity\Role;
use Tixi\SecurityBundle\Entity\RoleRepository;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class UserAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class UserAssembler {

    /**
     * @var EncoderFactory $encoderFactory
     */
    private $encoderFactory;
    /**
     * @var SecurityContext $securityContext
     */
    private $securityContext;

    /**
     * @param EncoderFactory $encoderFactory
     */
    public function setEncoderFactory(EncoderFactory $encoderFactory) {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }

    /**
     * @param UserRegisterDTO $userDTO
     * @param RoleRepository $roleRepository
     * @return User
     */
    public function registerDTOtoNewUser(UserRegisterDTO $userDTO, RoleRepository $roleRepository) {
        $user = User::registerUser(
            $userDTO->username,
            $userDTO->password,
            $userDTO->email);
        $this->encodeUserPassword($user, $userDTO->password);
        $this->assignRolesFromSelection($user, $userDTO->role, $roleRepository);
        return $user;
    }

    /**
     * @param UserEditDTO $userDTO
     * @param User $user
     * @param \Tixi\SecurityBundle\Entity\RoleRepository $roleRepository
     * @return User
     */
    public function registerEditDTOtoUser(UserEditDTO $userDTO, User $user, RoleRepository $roleRepository) {
        $user->updateBasicData(
            $userDTO->username,
            $userDTO->email);
        if (!empty($userDTO->password)) {
            $this->encodeUserPassword($user, $userDTO->password);
        }
        $this->assignRolesFromSelection($user, $userDTO->role, $roleRepository);
        return $user;
    }

    /**
     * @param UserProfileDTO $userDTO
     * @param User $user
     * @return User
     */
    public function registerProfileDTOtoUser(UserProfileDTO $userDTO, User $user) {
        $user->updateBasicData(
            $userDTO->username,
            $userDTO->email);
        if (!empty($userDTO->new_password)) {
            $this->encodeUserPassword($user, $userDTO->new_password);
        }
        return $user;
    }

    /**
     * @param User $user
     * @return UserEditDTO
     */
    public function userToUserEditDTO(User $user) {
        $userDTO = new UserEditDTO();
        $userDTO->id = $user->getId();
        $userDTO->role = $user->getHighestRole();
        $userDTO->username = $user->getUsername();
        $userDTO->password = $user->getPassword();
        $userDTO->email = $user->getEmail();
        return $userDTO;
    }

    /**
     * @param User $user
     * @return UserProfileDTO
     */
    public function userToUserProfileDTO(User $user) {
        $userDTO = new UserProfileDTO();
        $userDTO->id = $user->getId();
        $userDTO->username = $user->getUsername();
        $userDTO->password = $user->getPassword();
        $userDTO->email = $user->getEmail();
        return $userDTO;
    }

    /**
     * @param $users
     * @internal param \Tixi\SecurityBundle\Entity\User $user
     * @return UserListDTO
     */
    public function usersToUserListDTOs($users) {
        $dtoArray = array();
        foreach ($users as $user) {
            if ($user->getId() == $this->securityContext->getToken()->getUser()->getId()) {
                continue;
            }
            if ($user->getRoles() > $this->securityContext->getToken()->getRoles()) {
                continue;
            }
            $dtoArray[] = $this->toUserListDTO($user);

        }
        return $dtoArray;
    }

    /**
     * @param User $user
     * @return UserListDTO
     */
    public function toUserListDTO(User $user) {
        $userListDTO = new UserListDTO();
        $userListDTO->id = $user->getId();
        $userListDTO->username = $user->getUsername();
        $userListDTO->email = $user->getEmail();
        $userListDTO->roles = $user->getHighestRole()->getName();
        return $userListDTO;
    }

    /**
     * @param User $user
     * @param $password
     */
    private function encodeUserPassword(User $user, $password) {
        $encoder = $this->encoderFactory->getEncoder($user);
        $encPassword = $encoder->encodePassword($password, $user->getSalt());
        $user->setPassword($encPassword);
    }

    /**
     * @param User $user
     * @param $role
     * @param RoleRepository $roleRepository
     */
    private function assignRolesFromSelection(User $user, Role $role, RoleRepository $roleRepository) {
        $roleDispo = $roleRepository->findOneBy(array('role' => Role::$roleDispo));
        $roleManager = $roleRepository->findOneBy(array('role' => Role::$roleManager));
        $roleAdmin = $roleRepository->findOneBy(array('role' => Role::$roleAdmin));

        switch ($role->getName()) {
            case Role::$roleDispoName:
                $user->assignRole($roleDispo);
                $user->unsignRole($roleManager);
                $user->unsignRole($roleAdmin);
                break;
            case Role::$roleManagerName:
                $user->assignRole($roleDispo);
                $user->assignRole($roleManager);
                $user->unsignRole($roleAdmin);
                break;
            case Role::$roleAdminName:
                $user->assignRole($roleDispo);
                $user->assignRole($roleManager);
                $user->assignRole($roleAdmin);
                break;
        }
    }
}