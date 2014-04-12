<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:32
 */

namespace Tixi\SecurityBundle\Repository;


use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;
use Tixi\SecurityBundle\Entity\Role;
use Tixi\SecurityBundle\Entity\RoleRepository;

/**
 * Class RoleRepositoryDoctrine
 * @package Tixi\SecurityBundle\Repository
 */
class RoleRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RoleRepository {
    /**
     * @param Role $role
     * @return mixed|void
     */
    public function store(Role $role) {
        $this->getEntityManager()->persist($role);
    }

    /**
     * @param Role $role
     * @return mixed|void
     */
    public function remove(Role $role) {
        $this->getEntityManager()->remove($role);
    }
}