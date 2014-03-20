<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:32
 */

namespace Tixi\SecurityBundle\Repository;


use Tixi\SecurityBundle\Entity\User;
use Tixi\SecurityBundle\Entity\UserRepository;
use Doctrine\ORM\EntityRepository;

class UserRepositoryDoctrine extends EntityRepository implements UserRepository {

    public function find($id) {
        return parent::find($id);
    }

    public function findAll() {
        return $this->findAllBy();
    }

    public function store(User $user) {
        $this->getEntityManager()->persist($user);
    }

    public function remove(User $user) {
        $this->getEntityManager()->remove($user);
    }


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

    public function findOneBy(array $criteria, array $orderBy = null){
        return parent::findOneBy($criteria, $orderBy);
    }
}