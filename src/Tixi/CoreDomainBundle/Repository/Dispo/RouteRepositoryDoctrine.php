<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\RouteRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RouteRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RouteRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RouteRepository {
    /**
     * @param Route $route
     * @return mixed|void
     */
    public function store(Route $route) {
        $this->getEntityManager()->persist($route);
    }

    /**
     * @param Route $route
     * @return mixed|void
     */
    public function remove(Route $route) {
        $this->getEntityManager()->remove($route);
    }

    /**
     * @param Address $from
     * @param Address $to
     * @return Route
     */
    public function findRouteWithAddresses(Address $from, Address $to) {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startAddress = :startAddressId')
            ->andWhere('e.targetAddress = :targetAddressId')
            ->setParameter('startAddressId', $from->getId())
            ->setParameter('targetAddressId', $to->getId());
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Stores Route if none exist and get result
     * @param \Tixi\CoreDomain\Dispo\Route $route
     * @return Route
     */
    public function storeRouteIfNotExist(Route $route) {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startAddress = :startAddressId')
            ->andWhere('e.targetAddress = :targetAddressId')
            ->setParameter('startAddressId', $route->getStartAddress()->getId())
            ->setParameter('targetAddressId', $route->getTargetAddress()->getId());
        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result) {
            return $result;
        } else {
            $this->store($route);
            return $route;
        }
    }

    /**
     * @return Route[]
     */
    public function findRoutesOlderThenOneMonth(){
        $now = new \DateTime();
        $pastMonth = $now->modify('-1 month');
        $qb = parent::createQueryBuilder('r');
        $qb->where('r.modifiedDateTime <= :pastMonth')
            ->andWhere('r.isDeleted = 0')
            ->setParameter('pastMonth', $pastMonth->format('Y-m-d'));
        return $qb->getQuery()->getResult();

    }
}