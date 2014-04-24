<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface PersonRepository
 * @package Tixi\CoreDomain
 */
interface PersonRepository extends CommonBaseRepository{
    /**
     * @param Person $person
     * @return mixed
     */
    public function store(Person $person);

    /**
     * @param Person $person
     * @return mixed
     */
    public function remove(Person $person);
}