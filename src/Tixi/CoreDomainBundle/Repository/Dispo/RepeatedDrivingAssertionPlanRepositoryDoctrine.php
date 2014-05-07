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

/**
 * Class RepeatedDrivingAssertionPlanRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingAssertionPlanRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingAssertionPlanRepository{
    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed|void
     */
    public function store(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->persist($assertionPlan);
    }

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed|void
     */
    public function remove(RepeatedDrivingAssertionPlan $assertionPlan)
    {
        $this->getEntityManager()->remove($assertionPlan);
    }

    /**
     * @param \DateTime $date
     * @return RepeatedDrivingAssertionPlan[]
     */
    public function findPlanForDate(\DateTime $date) {
        $qb = parent::createQueryBuilder('p');
        $qb->where('p.anchorDate <= :date')->andWhere('p.endingDate >= :date');
        $qb->setParameter('date', $date->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }
}