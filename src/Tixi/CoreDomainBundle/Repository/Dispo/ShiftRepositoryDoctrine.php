<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ShiftRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ShiftRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements ShiftRepository {
    /**
     * @param Shift $shift
     */
    public function store(Shift $shift) {
        $this->getEntityManager()->persist($shift);
    }

    /**
     * @param Shift $shift
     */
    public function remove(Shift $shift) {
        $this->getEntityManager()->remove($shift);
    }
}