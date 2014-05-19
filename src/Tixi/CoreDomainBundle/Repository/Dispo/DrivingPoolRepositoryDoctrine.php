<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\DrivingPoolRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingPoolRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class DrivingPoolRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DrivingPoolRepository {
    /**
     * @param DrivingPool $drivingPool
     * @return mixed|void
     */
    public function store(DrivingPool $drivingPool) {
        $this->getEntityManager()->persist($drivingPool);
    }

    /**
     * @param DrivingPool $drivingPool
     * @return mixed|void
     */
    public function remove(DrivingPool $drivingPool) {
        $this->getEntityManager()->remove($drivingPool);
    }

}