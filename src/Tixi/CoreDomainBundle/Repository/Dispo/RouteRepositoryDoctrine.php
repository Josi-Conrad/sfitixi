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
}