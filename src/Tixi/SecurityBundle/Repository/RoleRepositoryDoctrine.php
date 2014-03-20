<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:32
 */

namespace Tixi\SecurityBundle\Repository;


use Tixi\SecurityBundle\Entity\Role;
use Tixi\SecurityBundle\Entity\RoleRepository;
use Doctrine\ORM\EntityRepository;

class RoleRepositoryDoctrine extends EntityRepository implements RoleRepository {

    public function find($id) {
        return parent::find($id);
    }

    public function findAll() {
        return $this->findAllBy();
    }

    public function store(Role $role) {
        $this->getEntityManager()->persist($role);
    }

    public function remove(Role $role) {
        $this->getEntityManager()->remove($role);
    }

    public function findOneBy(array $criteria, array $orderBy = null){
        return parent::findOneBy($criteria, $orderBy);
    }
}