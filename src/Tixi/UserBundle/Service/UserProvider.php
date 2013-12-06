<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 02.12.13
 * Time: 17:04
 * To change this template use File | Settings | File Templates.
 */

// src/Tixi/UserBundle/Service/UserProvider.php
namespace Tixi\UserBundle\Service;

use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Tixi\UserBundle\Service\MyUser;
// dependancy injection: UserData
use Tixi\UserBundle\Controller\UserData;

class UserProvider implements UserProviderInterface
{/*
  * User Class Provider as defined in Symfony2 boilerplate
  * http://symfony.com/doc/current/cookbook/security/custom_provider.html
  */

    protected $userdata;       // dependancy injection
    protected $usercache = array(); // cached user data

    public function __construct(UserData $userdata)
    {
        $this->userdata = $userdata;
    }

    private function getUserdata($username)
    {
        /* make a call to your database service
         * if the user doesn't exist an exception is thrown
         * user data is cached and refreshed when the username changes
         */

        if ((!array_key_exists('benutzername', $this->usercache)) or
            ($this->usercache['benutzername'] != $username ))
        {
            $myrecord = $this->userdata->getUserData($username);
            $this->usercache = $myrecord[0];
        }
        return $this->usercache;
    }

    public function loadUserByUsername($username)
    {
        $uarray = $this->getUserdata($username);
        if (is_array($uarray))
        {
            if (!$uarray['ist_aktive']) { $uarray['passwort'] = 'password-is-expired'; }
            $salt = getenv("APACHE_SALT");
            $roles = array();
            if ($uarray['ist_manager']) { $roles[] = 'ROLE_ADMIN'; }
            if ($uarray['ist_disponent']) { $roles[] = 'ROLE_USER'; }
            return new MyUser($uarray['benutzername'], $uarray['passwort'], $salt, $roles);
        }
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Tixi\UserBundle\Service\MyUser';
        // return $class === 'Tixi\UserBundle\Service\TixiUser';
        // return $class === 'Symfony\Component\Security\Core\User\User';
    }

}