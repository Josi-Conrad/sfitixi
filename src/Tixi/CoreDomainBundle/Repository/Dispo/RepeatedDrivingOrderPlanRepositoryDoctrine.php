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

/**
 * Class RepeatedDrivingOrderPlanRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingOrderPlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingOrderPlanRepository{
    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     * @return mixed|void
     */
    public function store(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan)
    {
        $this->getEntityManager()->persist($repeatedDrivingOrderPlan);
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     * @return mixed|void
     */
    public function remove(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan)
    {
        $this->getEntityManager()->remove($repeatedDrivingOrderPlan);
    }
}