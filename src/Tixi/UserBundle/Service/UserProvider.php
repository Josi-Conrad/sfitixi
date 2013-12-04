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

    public function loadUserByUsername($username)
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
        $userdata[] = array(
            'username' => 'admin@btb.ch',
            'password' => 'admin',
            'roles' => array('ROLE_USER', 'ROLE_ADMIN'));

        // pretend it returns an array on success, false if there is no user

        foreach ($userdata as $key => $values)
        {
            if ($values['username'] == $username)
            {
                $salt = getenv("APACHE_SALT");
                return new User($values['username'], $values['password'], $values['roles']);
            }
        }
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        // if (!$user instanceof WebserviceUser) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }

}