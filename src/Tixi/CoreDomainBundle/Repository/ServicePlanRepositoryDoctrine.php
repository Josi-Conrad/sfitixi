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

class ServicePlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements ServicePlanRepository {



    public function store(ServicePlan $servicePlan)
    {
        $this->getEntityManager()->persist($servicePlan);
    }

    public function remove(ServicePlan $servicePlan)
    {
        $this->getEntityManager()->remove($servicePlan);
    }


//    public function findAllByParent($parentEntity, array $orderBy = null, $page = null, $limit = null, $filterStr = null, $sourceDTO = null)
//    {
//        $correctedPage = (is_null($page)) ? null : $page-1;
//        $servicePlans = $parentEntity->getAssociatedServicePlans();
//        $criteria = Criteria::create();
//        if(!is_null($filterStr) && !is_null($sourceDTO)) {
//            foreach($sourceDTO as $field=>$value) {
//                $criteria->orWhere(Criteria::expr()->contains($field,$filterStr));
//            }
//        }
//        $criteria->orderBy($orderBy);
//        $criteria->setFirstResult($correctedPage*$limit);
//        $criteria->setMaxResults($limit);
//        $filteredServicePlans =  $servicePlans->matching($criteria);
//        return $filteredServicePlans;
//    }
//
//    public function getNumberOfTotalRecordsByParent($parentEntity, $filterStr = null, $sourceDTO = null)
//    {
//        $servicePlans = $parentEntity->getAssociatedServicePlans();
//        $criteria = Criteria::create();
//        if(!is_null($filterStr) && !is_null($sourceDTO)) {
//            foreach($sourceDTO as $field=>$value) {
//                $criteria->orWhere(Criteria::expr()->contains($field,$filterStr));
//            }
//        }
//        return $servicePlans->matching($criteria)->count();
//    }

}