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
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingAssertionRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
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

    /**
     * @param Shift $shift
     * @return array|mixed
     */
    public function findAllActiveByShift(Shift $shift)
    {
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.shift = :shift')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('shift', $shift);
        return $qb->getQuery()->getResult();
    }

    public function findAllProspectiveByDriver(Driver $driver) {
        $now = new \DateTime();
        $qb = parent::createQueryBuilder('e');
        $qb->join('e.driver', 'd')
            ->join('e.shift','s')
            ->join('s.workingDay','w')
            ->where('d = :driver')
            ->andWhere('w.date >= :now')
            ->setParameter('driver',$driver)
            ->setParameter('now',$now->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }


    /**
     * @param RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan
     * @return array|mixed
     */
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