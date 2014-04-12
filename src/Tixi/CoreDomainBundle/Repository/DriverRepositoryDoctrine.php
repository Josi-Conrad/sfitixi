<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverRepository;

/**
 * Class DriverRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class DriverRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverRepository {
    /**
     * @param Driver $driver
     * @return mixed|void
     */
    public function store(Driver $driver) {
        $this->getEntityManager()->persist($driver);
    }

    /**
     * @param Driver $driver
     * @return mixed|void
     */
    public function remove(Driver $driver) {
        $this->getEntityManager()->remove($driver);
    }

}