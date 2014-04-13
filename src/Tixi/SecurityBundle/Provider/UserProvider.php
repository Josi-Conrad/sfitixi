<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:37
 */

namespace Tixi\SecurityBundle\Provider;


use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tixi\SecurityBundle\Entity\UserRepository;

/**
 * Class UserProvider
 * @package Tixi\SecurityBundle\Provider
 */
class UserProvider implements UserProviderInterface {

    /**
     * @var \Tixi\SecurityBundle\Entity\UserRepository
     */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     * @return mixed|UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username) {
        $user = $this->userRepository->findOneBy(array('username' => $username));
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        return $user;
    }

    /**
     * @param UserInterface $user
     * @return mixed|UserInterface
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }
        return $this->userRepository->find($user->getId());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class) {
        return $this->userRepository->getClassName() === $class
        || is_subclass_of($class, $this->userRepository->getClassName());
    }
}