<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\PersonCategory;
use Tixi\CoreDomain\PersonCategoryRepository;

/**
 * Class PersonCategoryRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class PersonCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PersonCategoryRepository {
    /**
     * @param PersonCategory $personCategory
     * @return mixed|void
     */
    public function store(PersonCategory $personCategory) {
        $this->getEntityManager()->persist($personCategory);
    }

    /**
     * @param PersonCategory $personCategory
     * @return mixed|void
     */
    public function remove(PersonCategory $personCategory) {
        $this->getEntityManager()->remove($personCategory);
    }

    /**
     * @param PersonCategory $personCategory
     * @return PersonCategory
     */
    public function storeAndGetPersonCategory(PersonCategory $personCategory) {
        $current = $this->findOneBy(array('name' => $personCategory->getName()));
        if (empty($current)) {
            $this->getEntityManager()->persist($personCategory);
            return $personCategory;
        }
        return $current;
    }
}