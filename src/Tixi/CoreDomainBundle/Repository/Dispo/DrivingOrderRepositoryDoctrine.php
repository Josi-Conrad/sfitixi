<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingOrderRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class DrivingOrderRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingOrderRepository {
    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function store(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->persist($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function remove(DrivingOrder $drivingOrder) {
        $this->getEntityManager()->remove($drivingOrder);
    }

    /**
     * @param \DateTime $day
     * @return DrivingOrder[]
     */
    public function findAllOrdersForDay(\DateTime $day){
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.pickUpDate = :day')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('day', $day->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }
}