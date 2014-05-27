<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\ShiftTypeRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ShiftTypeRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ShiftTypeRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements ShiftTypeRepository {
    /**
     * @param ShiftType $shiftType
     * @return mixed|void
     */
    public function store(ShiftType $shiftType) {
        $this->getEntityManager()->persist($shiftType);
    }

    /**
     * @param ShiftType $shiftType
     * @return mixed|void
     */
    public function remove(ShiftType $shiftType) {
        $this->getEntityManager()->remove($shiftType);
    }

    /**
     * @return ShiftType[]
     */
    public function findAllActive(){
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.isDeleted = 0');
        return $qb->getQuery()->getResult();
    }

}