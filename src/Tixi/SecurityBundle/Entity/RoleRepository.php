<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:34
 */

namespace Tixi\SecurityBundle\Entity;

/**
 * Interface RoleRepository
 * @package Tixi\SecurityBundle\Entity
 */
interface RoleRepository {
    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @return mixed
     */
    public function findAll();

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param Role $role
     * @return mixed
     */
    public function store(Role $role);

    /**
     * @param Role $role
     * @return mixed
     */
    public function remove(Role $role);

}