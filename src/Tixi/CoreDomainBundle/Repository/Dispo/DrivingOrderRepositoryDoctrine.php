<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingOrderRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class DrivingOrderRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingOrderRepository {
    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function store(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->persist($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function remove(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->remove($drivingOrder);
    }

    public function findAllOrdersForShift(Shift $shift)
    {
        // TODO: Implement findAllOrdersForShift() method.
    }
}