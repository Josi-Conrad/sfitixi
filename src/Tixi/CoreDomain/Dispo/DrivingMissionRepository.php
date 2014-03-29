<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DrivingMissionRepository extends CommonBaseRepository {

    public function store(DrivingMission $drivingMission);

    public function remove(DrivingMission $drivingMission);

}