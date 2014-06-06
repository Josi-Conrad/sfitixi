<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\InsuranceRepository;

/**
 * Class InsuranceRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class InsuranceRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements InsuranceRepository {
    /**
     * @param Insurance $insurance
     * @return mixed|void
     */
    public function store(Insurance $insurance) {
        $this->getEntityManager()->persist($insurance);
    }

    /**
     * @param Insurance $insurance
     * @return mixed|void
     */
    public function remove(Insurance $insurance) {
        $this->getEntityManager()->remove($insurance);
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