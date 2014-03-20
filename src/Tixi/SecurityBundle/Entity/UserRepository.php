<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 19:34
 */

namespace Tixi\SecurityBundle\Entity;


interface UserRepository {

    public function find($id);

    public function findAll();

    public function findByUserName($username);

    public function findOneBy(array $criteria, array $orderBy = null);

    public function store(User $user);

    public function remove(User $user);

}