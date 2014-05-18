<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\DriverRepository;

/**
 * Class DriverRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class DriverRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverRepository {
    /**
     * @param Driver $driver
     * @return mixed|void
     */
    public function store(Driver $driver) {
        $this->getEntityManager()->persist($driver);
    }

    /**
     * @param Driver $driver
     * @return mixed|void
     */
    public function remove(Driver $driver) {
        $this->getEntityManager()->remove($driver);
    }

    /**
     * @param \Tixi\CoreDomain\DriverCategory $category
     * @return mixed
     */
    public function getAmountByDriverCategory(DriverCategory $category) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.driverCategory = :category');
        $qb->setParameter('category', $category);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Driver[]
     */
    public function findAllActive() {
        $qb = parent::createQueryBuilder('e');
        $qb->select()
            ->where('e.isDeleted = 0');
        return $qb->getQuery()->getResult();
    }
}