<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:38
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface RepeatedDrivingOrderPlanRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface RepeatedDrivingOrderPlanRepository extends CommonBaseRepository{
    /**
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     * @return mixed
     */
    public function store(RepeatedDrivingOrderPlan $drivingOrderPlan);

    /**
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     * @return mixed
     */
    public function remove(RepeatedDrivingOrderPlan $drivingOrderPlan);

}