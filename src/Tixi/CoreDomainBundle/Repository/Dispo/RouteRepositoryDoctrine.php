<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\RouteRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RouteRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RouteRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements RouteRepository {
    /**
     * @param Route $route
     */
    public function store(Route $route) {
        $this->getEntityManager()->persist($route);
    }

    /**
     * @param Route $route
     */
    public function remove(Route $route) {
        $this->getEntityManager()->remove($route);
    }
}