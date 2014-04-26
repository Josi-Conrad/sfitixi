<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DrivingPoolRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingPoolRepository extends CommonBaseRepository {
    /**
     * @param DrivingPool $drivingPool
     * @return mixed
     */
    public function store(DrivingPool $drivingPool);

    /**
     * @param DrivingPool $drivingPool
     * @return mixed
     */
    public function remove(DrivingPool $drivingPool);

}