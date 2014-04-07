<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Tixi\SecurityBundle\Entity\User;

class UserAssembler {
    /**
     * @param UserRegisterDTO $userDTO
     * @return User
     */
    public function registerDTOtoNewUser(UserRegisterDTO $userDTO) {
        $user = User::registerUser(
            $userDTO->username,
            $userDTO->password,
            $userDTO->email);
        return $user;
    }

    /**
     * @param UserRegisterDTO $userDTO
     * @param User $user
     * @return User
     */
    public function registerDTOtoUser(UserRegisterDTO $userDTO, User $user) {
        $user->updateBasicData(
            $userDTO->username,
            $userDTO->password,
            $userDTO->email);
        return $user;
    }

    /**
     * @param User $user
     * @return UserRegisterDTO
     */
    public function userToUserRegisterDTO(User $user) {
        $userDTO = new UserRegisterDTO();
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

    public function toUserListDTO(User $user) {
        $userListDTO = new UserListDTO();
        $userListDTO->id = $user->getId();
        $userListDTO->username = $user->getUsername();
        $userListDTO->email = $user->getEmail();
        $userListDTO->roles = $this->rolesToString($user->getRolesEntity());
        return $userListDTO;
    }

    private function rolesToString($roles) {
        $string = '| ';
        foreach ($roles as $role) {
            $string .= $role->getName() . ' | ';
        }
        return $string;
    }
}