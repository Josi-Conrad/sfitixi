<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverRepository;

class DriverRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverRepository {

    public function store(Driver $driver) {
        $this->getEntityManager()->persist($driver);
    }

    public function remove(Driver $driver) {
        $this->getEntityManager()->remove($driver);
    }
}