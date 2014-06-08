<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Symfony\Component\Validator\Constraints\DateTime;
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
     * @return WorkingMonth
     */
    public function findWorkingMonthByDate(\DateTime $date) {
        $startMonth = $date->modify('first day of this month');
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.date >= :correctedDate')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('correctedDate', $startMonth->format('Y-m-d'));
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return WorkingMonth[]
     */
    public function findProspectiveWorkingMonths() {
        $now = new \DateTime();
        $startMonth = $now->modify('first day of this month');
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.date >= :startMonth')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('startMonth', $startMonth->format('Y-m-d'));
        return $qb->getQuery()->getResult();


    }
}