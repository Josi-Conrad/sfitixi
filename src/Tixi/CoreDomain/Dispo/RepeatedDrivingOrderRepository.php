<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface RepeatedDrivingOrderRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface RepeatedDrivingOrderRepository extends CommonBaseRepository, DrivingOrderRepositoryInterface {
    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     * @return mixed
     */
    public function store(RepeatedDrivingOrder $repeatedDrivingOrder);

    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     * @return mixed
     */
    public function remove(RepeatedDrivingOrder $repeatedDrivingOrder);

}