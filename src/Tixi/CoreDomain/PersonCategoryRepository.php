<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface PersonCategoryRepository
 * @package Tixi\CoreDomain
 */
interface PersonCategoryRepository extends CommonBaseRepository{
    /**
     * @param PersonCategory $personCategory
     * @return mixed
     */
    public function store(PersonCategory $personCategory);

    /**
     * @param PersonCategory $personCategory
     * @return mixed
     */
    public function remove(PersonCategory $personCategory);

}