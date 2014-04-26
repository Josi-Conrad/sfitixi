<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DrivingMissionRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingMissionRepository extends CommonBaseRepository {
    /**
     * @param DrivingMission $drivingMission
     * @return mixed
     */
    public function store(DrivingMission $drivingMission);

    /**
     * @param DrivingMission $drivingMission
     * @return mixed
     */
    public function remove(DrivingMission $drivingMission);

}