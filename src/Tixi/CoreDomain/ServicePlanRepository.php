<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 12:17
 */

namespace Tixi\CoreDomain;



use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface ServicePlanRepository extends CommonBaseRepository{

    public function store(ServicePlan $servicePlan);

    public function remove(ServicePlan $servicePlan);
} 