<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 18:00
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Doctrine\Common\Collections\Criteria;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlanRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RepeatedDrivingAssertionPlanRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingAssertionPlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingAssertionPlanRepository{
    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed|void
     */
    public function store(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->persist($assertionPlan);
    }

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed|void
     */
    public function remove(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->remove($assertionPlan);
    }

    /**
     * @param \DateTime $date
     * @return RepeatedDrivingAssertionPlan[]
     */
    public function findPlanForDate(\DateTime $date) {
        $qb = parent::createQueryBuilder('p');
        $qb->where('p.anchorDate <= :date')->andWhere('p.endingDate >= :date');
        $qb->setParameter('date', $date->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }

    public function findActivePlansInRangeOfWorkingMonth(WorkingMonth $workingMonth)
    {
        /** @var \DateTime $startDate */
        $startDate = clone $workingMonth->getDate();
        /** @var \DateTime $endDate */
        $endDate = clone $workingMonth->getDate();
        $endDate->modify('last day of this month');

        $qb = parent::createQueryBuilder('p');
        $qb->where('p.anchorDate <= :endDate')->andWhere('p.endingDate >= :startDate');
        $qb->setParameter('startDate',$startDate->format('Y-m-d'));
        $qb->setParameter('endDate',$endDate->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }

    public function findAllProspectiveForDriver(Driver $driver)
    {
        $now = new \DateTime();
        $qb = parent::createQueryBuilder('p');
        $qb->where('p.driver = :driver')
            ->andWhere('p.endingDate >= :now')
            ->andWhere('p.isDeleted = 0')
            ->setParameter('driver',$driver)
            ->setParameter('now',$now->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }
}