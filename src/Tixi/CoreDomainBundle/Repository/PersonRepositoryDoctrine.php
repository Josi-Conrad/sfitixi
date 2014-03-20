<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Person;
use Tixi\CoreDomain\PersonRepository;

class PersonRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PersonRepository {

    public function store(Person $person) {
        $this->getEntityManager()->persist($person);
    }

    public function remove(Person $person) {
        $this->getEntityManager()->remove($person);
    }
}