<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class DrivingMissionRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingMissionRepository {

    public function store(DrivingMission $drivingMission) {
        $this->getEntityManager()->persist($drivingMission);
    }

    public function remove(DrivingMission $drivingMission) {
        $this->getEntityManager()->remove($drivingMission);
    }
}