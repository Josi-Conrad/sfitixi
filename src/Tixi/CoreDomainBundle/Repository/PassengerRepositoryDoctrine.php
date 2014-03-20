<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\PassengerRepository;

class PassengerRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PassengerRepository {

    public function store(Passenger $passenger) {
        $this->getEntityManager()->persist($passenger);
    }

    public function remove(Passenger $passenger) {
        $this->getEntityManager()->remove($passenger);
    }
}