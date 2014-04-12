<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 12:17
 */

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface ServicePlanRepository
 * @package Tixi\CoreDomain
 */
interface ServicePlanRepository extends CommonBaseRepository{
    /**
     * @param ServicePlan $servicePlan
     * @return mixed
     */
    public function store(ServicePlan $servicePlan);

    /**
     * @param ServicePlan $servicePlan
     * @return mixed
     */
    public function remove(ServicePlan $servicePlan);
} 