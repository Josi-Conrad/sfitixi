<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 18:00
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Doctrine\Common\Collections\Criteria;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlanRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class RepeatedDrivingAssertionPlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingAssertionPlanRepository{

    public function store(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->persist($assertionPlan);
    }

    public function remove(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->remove($assertionPlan);
    }
}