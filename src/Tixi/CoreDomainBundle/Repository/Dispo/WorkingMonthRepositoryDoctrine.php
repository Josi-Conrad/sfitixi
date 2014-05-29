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
     * @return mixed
     */
    public function findWorkingMonthByDate(\DateTime $date) {
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.date = :correctedDate')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('correctedDate', $date->modify('first day of this month')->format('Y-m-d'));
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findNextActiveWorkingMonths($limit = 3)
    {
        $workingMonths = array();

        $startMonth = new \DateTime();
        $startMonth = $startMonth->modify('first day of this month');
        $endMonth = new \DateTime();
        $endMonth = $endMonth->modify('first day of +'.$limit.' month');
        $interval = new \DateInterval('P1M');
        $monthPeriode = new \DatePeriod($startMonth, $interval, $endMonth);
        foreach($monthPeriode as $month) {
            $workingMonth = $this->findWorkingMonthByDate($month);
            if(null !== $workingMonth) {
                $workingMonths[] = $workingMonth;
            }
        }
        return $workingMonths;
    }
}