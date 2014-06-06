<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\DriverCategoryRepository;

/**
 * Class DriverCategoryRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class DriverCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverCategoryRepository {
    /**
     * @param DriverCategory $driverCategory
     * @return mixed|void
     */
    public function store(DriverCategory $driverCategory) {
        $this->getEntityManager()->persist($driverCategory);
    }

    /**
     * @param DriverCategory $driverCategory
     * @return mixed|void
     */
    public function remove(DriverCategory $driverCategory) {
        $this->getEntityManager()->remove($driverCategory);
    }

    /**
     * @param DriverCategory $driverCategory
     * @return DriverCategory
     */
    public function storeAndGetDriverCategory(DriverCategory $driverCategory) {
        $current = $this->findOneBy(array('name' => $driverCategory->getName()));
        if (empty($current)) {
            $this->getEntityManager()->persist($driverCategory);
            return $driverCategory;
        }
        return $current;
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