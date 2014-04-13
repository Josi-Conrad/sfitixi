<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DriverRepository
 * @package Tixi\CoreDomain
 */
interface DriverRepository extends CommonBaseRepository{
    /**
     * @param Driver $driver
     * @return mixed
     */
    public function store(Driver $driver);

    /**
     * @param Driver $driver
     * @return mixed
     */
    public function remove(Driver $driver);
} 