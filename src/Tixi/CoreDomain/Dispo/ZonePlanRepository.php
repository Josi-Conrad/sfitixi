<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface ZonePlanRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface ZonePlanRepository extends CommonBaseRepository {
    /**
     * @param ZonePlan $zonePlan
     * @return mixed
     */
    public function store(ZonePlan $zonePlan);

    /**
     * @param ZonePlan $zonePlan
     * @return mixed
     */
    public function remove(ZonePlan $zonePlan);

}