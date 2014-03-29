<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface RouteRepository extends CommonBaseRepository {

    public function store(Route $route);

    public function remove(Route $route);

}