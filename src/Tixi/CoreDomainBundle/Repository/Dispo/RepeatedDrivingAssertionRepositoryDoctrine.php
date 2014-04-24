<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 08.04.14
 * Time: 21:09
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionRepository;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RepeatedDrivingAssertionRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingAssertionRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingAssertionRepository {
    /**
     * @param RepeatedDrivingAssertion $assertion
     * @return mixed|void
     */
    public function store(RepeatedDrivingAssertion $assertion) {
        $this->getEntityManager()->persist($assertion);
    }

    /**
     * @param RepeatedDrivingAssertion $assertion
     * @return mixed|void
     */
    public function remove(RepeatedDrivingAssertion $assertion) {
        $this->getEntityManager()->remove($assertion);
    }

    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function getAmountByShiftType(ShiftType $shiftType) {
        $qb = parent::createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->join('e.shiftTypes', 'h');
        $qb->where('h = :shiftType');
        $qb->setParameter('shiftType', $shiftType);
        return $qb->getQuery()->getSingleScalarResult();
    }
}