<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingOrderRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class DrivingOrderRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingOrderRepository {
    /**
     * @param DrivingOrder $drivingOrder
     */
    public function store(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->persist($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function remove(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->remove($drivingOrder);
    }
}