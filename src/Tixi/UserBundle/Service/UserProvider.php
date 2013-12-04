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

class UserProvider implements UserProviderInterface
{/*
  * User Class Provider as defined in Symfony2 boilerplate
  * http://symfony.com/doc/current/cookbook/security/custom_provider.html
  */

    private function getUserdata($username)
    {
        // make a call to your webservice or whatever here

        $userdata[] = array(
            'username' => 'martin@btb.ch',
            'password' => 'martin',
            'roles' => array('ROLE_USER'));
        $userdata[] = array(
            'username' => 'josi@btb.ch',
            'password' => 'josi',
            'roles' => array('ROLE_USER'));
      // pretend it returns an array on success, false if there is no user
        foreach ($userdata as $key => $values)
        {
            if ($values['username'] == $username)
            {
                return $values;
            }
        }
        return false;
    }

    public function loadUserByUsername($username)
    {
        $uarray = $this->getUserdata($username);
        if (is_array($uarray))
        {
            $salt = getenv("APACHE_SALT");
            $salt = null; // todo: remove this line of code
            return new MyUser($uarray['username'], $uarray['password'], $salt, $uarray['roles']);
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