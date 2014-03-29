<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class ShiftRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements ShiftRepository {

    public function store(Shift $shift) {
        $this->getEntityManager()->persist($shift);
    }

    public function remove(Shift $shift) {
        $this->getEntityManager()->remove($shift);
    }
}