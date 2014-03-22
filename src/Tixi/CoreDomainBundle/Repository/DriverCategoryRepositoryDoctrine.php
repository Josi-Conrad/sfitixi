<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\DriverCategoryRepository;

class DriverCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements DriverCategoryRepository {

    public function store(DriverCategory $driverCategory) {
        $this->getEntityManager()->persist($driverCategory);
    }

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
}