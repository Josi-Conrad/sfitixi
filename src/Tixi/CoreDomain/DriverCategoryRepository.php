<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DriverCategoryRepository
 * @package Tixi\CoreDomain
 */
interface DriverCategoryRepository extends CommonBaseRepository{
    /**
     * @param DriverCategory $driverCategory
     * @return mixed
     */
    public function store(DriverCategory $driverCategory);

    /**
     * @param DriverCategory $driverCategory
     * @return mixed
     */
    public function remove(DriverCategory $driverCategory);
    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name);
}