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
     * @param EncoderFactory $encoderFactory
     */
    public function setEncoderFactory(EncoderFactory $encoderFactory) {
        $this->encoderFactory = $encoderFactory;
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
        $this->encodeUserPassword($user);
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
            $user->updatePassword($userDTO->password);
            $this->encodeUserPassword($user);
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
            $user->updatePassword($userDTO->new_password);
            $this->encodeUserPassword($user);
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
        $userListDTO->roles = $this->rolesToString($user->getRolesEntity());
        return $userListDTO;
    }

    /**
     * @param User $user
     */
    private function encodeUserPassword(User $user) {
        $encoder = $this->encoderFactory->getEncoder($user);
        $encPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($encPassword);
    }

    /**
     * @param $roles
     * @return string
     */
    private function rolesToString($roles) {
        $string = '| ';
        foreach ($roles as $role) {
            $string .= $role->getRole() . ' | ';
        }
        return $string;
    }

    /**
     * @param User $user
     * @param $role
     * @param RoleRepository $roleRepository
     */
    private function assignRolesFromSelection(User $user, Role $role, RoleRepository $roleRepository) {
        $roleUser = $roleRepository->findOneBy(array('role' => Role::$roleUser));
        $roleDispo = $roleRepository->findOneBy(array('role' => Role::$roleDispo));
        $roleManager = $roleRepository->findOneBy(array('role' => Role::$roleManager));
        $roleAdmin = $roleRepository->findOneBy(array('role' => Role::$roleAdmin));

        switch ($role->getName()) {
            case Role::$roleUserName:
                $user->assignRole($roleUser);
                $user->unsignRole($roleDispo);
                $user->unsignRole($roleManager);
                $user->unsignRole($roleAdmin);
                break;
            case Role::$roleDispoName:
                $user->assignRole($roleUser);
                $user->assignRole($roleDispo);
                $user->unsignRole($roleManager);
                $user->unsignRole($roleAdmin);
                break;
            case Role::$roleManagerName:
                $user->assignRole($roleUser);
                $user->assignRole($roleDispo);
                $user->assignRole($roleManager);
                $user->unsignRole($roleAdmin);
                break;
            case Role::$roleAdminName:
                $user->assignRole($roleUser);
                $user->assignRole($roleDispo);
                $user->assignRole($roleManager);
                $user->assignRole($roleAdmin);
                break;
        }
    }
}