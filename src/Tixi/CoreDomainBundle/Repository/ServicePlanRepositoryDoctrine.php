<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 09:41
 */

namespace Tixi\CoreDomainBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Tixi\CoreDomain\ServicePlan;
use Tixi\CoreDomain\ServicePlanRepository;
use Tixi\CoreDomain\Vehicle;

/**
 * Class ServicePlanRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class ServicePlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements ServicePlanRepository {
    /**
     * @param ServicePlan $servicePlan
     * @return mixed|void
     */
    public function store(ServicePlan $servicePlan)
    {
        $this->getEntityManager()->persist($servicePlan);
    }

    /**
     * @param ServicePlan $servicePlan
     * @return mixed|void
     */
    public function remove(ServicePlan $servicePlan)
    {
        $this->getEntityManager()->remove($servicePlan);
    }

}