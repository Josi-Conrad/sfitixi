<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\PassengerRepository;

/**
 * Class PassengerRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class PassengerRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PassengerRepository {
    /**
     * @param Passenger $passenger
     * @return mixed|void
     */
    public function store(Passenger $passenger) {
        $this->getEntityManager()->persist($passenger);
    }

    /**
     * @param Passenger $passenger
     * @return mixed|void
     */
    public function remove(Passenger $passenger) {
        $this->getEntityManager()->remove($passenger);
    }

    /**
     * @param Insurance $insurance
     * @return mixed
     */
    public function getAmountByInsurance(Insurance $insurance) {
        $qb = parent::createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->join('e.insurances', 'i');
        $qb->where('i = :insurance');
        $qb->setParameter('insurance', $insurance);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Handicap $handicap
     * @return mixed
     */
    public function getAmountByHandicap(Handicap $handicap) {
        $qb = parent::createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->join('e.handicaps', 'h');
        $qb->where('h = :handicap');
        $qb->setParameter('handicap', $handicap);
        return $qb->getQuery()->getSingleScalarResult();
    }
}