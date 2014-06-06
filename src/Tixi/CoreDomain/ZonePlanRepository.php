<?php

namespace Tixi\CoreDomain;

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
     * @param Zone $zone
     * @return mixed
     */
    public function getAmountByZone(Zone $zone);

    /**
     * @param ZonePlan $zonePlan
     * @return mixed
     */
    public function remove(ZonePlan $zonePlan);

    /**
     * @param Address $address
     * @return ZonePlan
     */
    public function getZonePlanForAddress(Address $address);

    /**
     * @param $city
     * @return mixed
     */
    public function findZonePlanForCity($city);

    /**
     * @param $city
     * @return bool
     */
    public function checkIfCityAlreadyExist($city);
}