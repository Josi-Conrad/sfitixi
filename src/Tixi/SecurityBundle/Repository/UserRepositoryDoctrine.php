<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:32
 */

namespace Tixi\SecurityBundle\Repository;


use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;
use Tixi\SecurityBundle\Entity\User;
use Tixi\SecurityBundle\Entity\UserRepository;

/**
 * Class UserRepositoryDoctrine
 * @package Tixi\SecurityBundle\Repository
 */
class UserRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements UserRepository {
    /**
     * @param User $user
     * @return mixed|void
     */
    public function store(User $user) {
        $this->getEntityManager()->persist($user);
    }

    /**
     * @param User $user
     * @return mixed|void
     */
    public function remove(User $user) {
        $this->getEntityManager()->remove($user);
    }

    /**
     * @param $username
     * @return mixed
     * @throws UsernameNotFoundException
     */
    public function findByUserName($username) {
        $userQuery = parent::createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery();

        try {
            $user = $userQuery->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active TixiSecurityBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }
        return $user;
    }

    /**
     * checks if username exists, whether its active or not
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name) {
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.username = :duplicateName')
            ->setParameter('duplicateName', $name);
        if ($qb->getQuery()->getResult()) {
            return true;
        } else {
            return false;
        }
    }
}