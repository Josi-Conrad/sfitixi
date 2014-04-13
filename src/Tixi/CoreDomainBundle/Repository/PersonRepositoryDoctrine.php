<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Person;
use Tixi\CoreDomain\PersonRepository;

/**
 * Class PersonRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class PersonRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PersonRepository {
    /**
     * @param Person $person
     * @return mixed|void
     */
    public function store(Person $person) {
        $this->getEntityManager()->persist($person);
    }

    /**
     * @param Person $person
     * @return mixed|void
     */
    public function remove(Person $person) {
        $this->getEntityManager()->remove($person);
    }
}