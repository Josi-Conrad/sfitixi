<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:54
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Tixi\CoreDomain\Dispo\DrivingAssertion;
use Tixi\CoreDomain\Dispo\DrivingAssertionRepository;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class DrivingAssertionRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DrivingAssertionRepository{

    /**
     * @param DrivingAssertion $drivingAssertion
     * @return mixed
     */
    public function store(DrivingAssertion $drivingAssertion)
    {
        $this->getEntityManager()->persist($drivingAssertion);
    }

    /**
     * @param DrivingAssertion $drivingAssertion
     * @return mixed
     */
    public function remove(DrivingAssertion $drivingAssertion)
    {
        $this->getEntityManager()->remove($drivingAssertion);
    }

    public function findAllActiveByShift(Shift $shift)
    {
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.shift = :shift')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('shift', $shift);
        return $qb->getQuery()->getResult();
    }

    public function findAllProspectiveByRepeatedDrivingAssertionPlan(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan)
    {
        $now = new \DateTime();
        $qb = parent::createQueryBuilder('e');
        $qb->join('e.repeatedDrivingAssertionPlan', 'p')
            ->join('e.shift','s')
            ->join('s.workingDay','w')
            ->where('p = :assertionPlan')
            ->andWhere('w.date >= :now')
            ->setParameter('assertionPlan',$repeatedDrivingAssertionPlan)
            ->setParameter('now',$now->format('Y-m-d'));
        return $qb->getQuery()->getResult();


    }
}