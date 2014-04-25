<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:38
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface RepeatedDrivingOrderPlanRepository extends CommonBaseRepository{

    public function store(RepeatedDrivingOrderPlan $drivingOrderPlan);

    public function remove(RepeatedDrivingOrderPlan $drivingOrderPlan);

}