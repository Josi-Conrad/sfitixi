<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\DrivingPoolRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class DrivingPoolRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingPoolRepository {

    public function store(DrivingPool $drivingPool) {
        $this->getEntityManager()->persist($drivingPool);
    }

    public function remove(DrivingPool $drivingPool) {
        $this->getEntityManager()->remove($drivingPool);
    }
}