<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DrivingOrderRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingOrderRepository extends CommonBaseRepository {
    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed
     */
    public function store(DrivingOrder $drivingOrder);

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed
     */
    public function remove(DrivingOrder $drivingOrder);

}