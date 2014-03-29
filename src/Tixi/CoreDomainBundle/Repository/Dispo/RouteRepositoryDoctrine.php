<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\RouteRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class RouteRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements RouteRepository {

    public function store(Route $route) {
        $this->getEntityManager()->persist($route);
    }

    public function remove(Route $route) {
        $this->getEntityManager()->remove($route);
    }
}