<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ShiftTypeRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ShiftTypeRepositoryDoctrine extends CommonBaseRepositoryDoctrine {
    /**
     * @param ShiftType $shiftType
     */
    public function store(ShiftType $shiftType) {
        $this->getEntityManager()->persist($shiftType);
    }

    /**
     * @param ShiftType $shiftType
     */
    public function remove(ShiftType $shiftType) {
        $this->getEntityManager()->remove($shiftType);
    }
}