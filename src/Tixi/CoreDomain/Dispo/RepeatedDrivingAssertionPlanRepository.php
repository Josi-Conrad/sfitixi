<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 17:59
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface RepeatedDrivingAssertionPlanRepository extends CommonBaseRepository {

    public function store(RepeatedDrivingAssertionPlan $assertionPlan);

    public function remove(RepeatedDrivingAssertionPlan $assertionPlan);
} 