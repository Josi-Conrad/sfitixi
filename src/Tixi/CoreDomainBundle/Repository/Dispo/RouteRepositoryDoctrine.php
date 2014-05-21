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
     * @param \Tixi\CoreDomain\Dispo\Route $route
     * @return bool
     */
    public function storeRouteIfNotExist(Route $route) {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startAddress = :startAddressId')
            ->andWhere('e.targetAddress = :targetAddressId')
            ->setParameter('startAddressId', $route->getStartAddress()->getId())
            ->setParameter('targetAddressId', $route->getTargetAddress()->getId());
        if (!$qb->getQuery()->getOneOrNullResult()) {
            $this->store($route);
            return true;
        }
        return false;
    }
}