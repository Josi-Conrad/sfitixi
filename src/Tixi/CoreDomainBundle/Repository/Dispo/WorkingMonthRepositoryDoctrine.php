<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class WorkingMonthRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class WorkingMonthRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements WorkingMonthRepository {
    /**
     * @param WorkingMonth $workingMonth
     * @return mixed|void
     */
    public function store(WorkingMonth $workingMonth) {
        $this->getEntityManager()->persist($workingMonth);
    }

    /**
     * @param WorkingMonth $workingMonth
     * @return mixed|void
     */
    public function remove(WorkingMonth $workingMonth) {
        $this->getEntityManager()->remove($workingMonth);
    }

    /**
     * @param \DateTime $date
     * @return mixed
     */
    public function findWorkingMonthByDate(\DateTime $date) {
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.date = :correctedDate')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('correctedDate', $date->modify('first day of this month')->format('Y-m-d'));
        return $qb->getQuery()->getOneOrNullResult();
    }
}