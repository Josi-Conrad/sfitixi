<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface RouteRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface RouteRepository extends CommonBaseRepository {
    /**
     * @param Route $route
     * @return mixed
     */
    public function store(Route $route);

    /**
     * @param Route $route
     * @return mixed
     */
    public function remove(Route $route);

    /**
     * @param Address $from
     * @param Address $to
     * @return Route
     */
    public function findRouteWithAddresses(Address $from, Address $to);

    /**
     * @param \Tixi\CoreDomain\Dispo\Route $route
     * @return bool
     */
    public function storeRouteIfNotExist(Route $route);
}