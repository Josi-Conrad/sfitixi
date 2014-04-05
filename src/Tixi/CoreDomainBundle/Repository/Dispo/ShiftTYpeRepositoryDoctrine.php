<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class ShiftTypeRepositoryDoctrine extends CommonBaseRepositoryDoctrine {

    public function store(ShiftType $shiftType) {
        $this->getEntityManager()->persist($shiftType);
    }

    public function remove(ShiftType $shiftType) {
        $this->getEntityManager()->remove($shiftType);
    }
}