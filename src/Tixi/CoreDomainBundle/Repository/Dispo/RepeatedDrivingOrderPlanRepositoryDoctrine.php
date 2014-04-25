<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:40
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlanRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class RepeatedDrivingOrderPlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingOrderPlanRepository{

    public function store(RepeatedDrivingOrderPlan $drivingOrderPlan)
    {
        $this->getEntityManager()->persist($drivingOrderPlan);
    }

    public function remove(RepeatedDrivingOrderPlan $drivingOrderPlan)
    {
        $this->getEntityManager()->remove($drivingOrderPlan);
    }
}