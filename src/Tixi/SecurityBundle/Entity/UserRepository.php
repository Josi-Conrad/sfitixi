<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:34
 */

namespace Tixi\SecurityBundle\Entity;

/**
 * Interface UserRepository
 * @package Tixi\SecurityBundle\Entity
 */
interface UserRepository {
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
     * @param $username
     * @return mixed
     */
    public function findByUserName($username);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return mixed
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * @param User $user
     * @return mixed
     */
    public function store(User $user);

    /**
     * @param User $user
     * @return mixed
     */
    public function remove(User $user);

}