<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class DrivingMissionRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class DrivingMissionRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements DrivingMissionRepository {
    /**
     * @param DrivingMission $drivingMission
     */
    public function store(DrivingMission $drivingMission) {
        $this->getEntityManager()->persist($drivingMission);
    }

    /**
     * @param DrivingMission $drivingMission
     */
    public function remove(DrivingMission $drivingMission) {
        $this->getEntityManager()->remove($drivingMission);
    }
}