<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface ZoneRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface ZoneRepository extends CommonBaseRepository {
    /**
     * @param Zone $zone
     * @return mixed
     */
    public function store(Zone $zone);

    /**
     * @param Zone $zone
     * @return mixed
     */
    public function remove(Zone $zone);

    public function findUnclassifiedZone();

}