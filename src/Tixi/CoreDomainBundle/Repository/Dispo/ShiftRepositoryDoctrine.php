<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ShiftRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ShiftRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements ShiftRepository {
    /**
     * @param Shift $shift
     * @return mixed|void
     */
    public function store(Shift $shift) {
        $this->getEntityManager()->persist($shift);
    }

    /**
     * @param Shift $shift
     * @return mixed|void
     */
    public function remove(Shift $shift) {
        $this->getEntityManager()->remove($shift);
    }

    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function getAmountByShiftType(ShiftType $shiftType) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.shiftType = :shiftType');
        $qb->setParameter('shiftType', $shiftType);
        return $qb->getQuery()->getSingleScalarResult();
    }
}