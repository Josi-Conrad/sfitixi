<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces\Management;

/**
 * Class UserProfileDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class UserProfileDTO {
    public $id;
    public $username;
    public $password;
    public $new_password_1;
    public $new_password_2;
    public $email;
}

