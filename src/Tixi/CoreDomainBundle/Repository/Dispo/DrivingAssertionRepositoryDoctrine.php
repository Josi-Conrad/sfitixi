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
}