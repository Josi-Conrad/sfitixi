<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\DriverCategoryRepository;

class DriverCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverCategoryRepository {

    public function store(DriverCategory $driverCategory) {
        $this->getEntityManager()->persist($driverCategory);
    }

    public function remove(DriverCategory $driverCategory) {
        $this->getEntityManager()->remove($driverCategory);
    }
}