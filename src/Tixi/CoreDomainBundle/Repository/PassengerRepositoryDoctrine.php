<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\PassengerRepository;

/**
 * Class PassengerRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class PassengerRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PassengerRepository {
    /**
     * @param Passenger $passenger
     * @return mixed|void
     */
    public function store(Passenger $passenger) {
        $this->getEntityManager()->persist($passenger);
    }

    /**
     * @param Passenger $passenger
     * @return mixed|void
     */
    public function remove(Passenger $passenger) {
        $this->getEntityManager()->remove($passenger);
    }
}