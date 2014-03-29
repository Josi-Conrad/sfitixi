<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class DrivingOrderRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingOrderRepository {

    public function store(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->persist($drivingOrder);
    }

    public function remove(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->remove($drivingOrder);
    }
}