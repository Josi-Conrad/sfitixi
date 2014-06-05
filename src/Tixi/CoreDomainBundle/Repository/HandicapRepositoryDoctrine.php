<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\HandicapRepository;

/**
 * Class HandicapRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class HandicapRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements HandicapRepository {
    /**
     * @param Handicap $handicap
     * @return mixed|void
     */
    public function store(Handicap $handicap) {
        $this->getEntityManager()->persist($handicap);
    }

    /**
     * @param Handicap $handicap
     * @return mixed|void
     */
    public function remove(Handicap $handicap) {
        $this->getEntityManager()->remove($handicap);
    }
    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name) {
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.isDeleted = 0')
            ->andWhere('s.name = :duplicateName')
            ->setParameter('duplicateName', $name);
        if ($qb->getQuery()->getResult()) {
            return true;
        } else {
            return false;
        }
    }
}