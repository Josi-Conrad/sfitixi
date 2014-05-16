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
     * @return mixed|void
     */
    public function store(DrivingMission $drivingMission) {
        $this->getEntityManager()->persist($drivingMission);
    }

    /**
     * @param DrivingMission $drivingMission
     * @return mixed|void
     */
    public function remove(DrivingMission $drivingMission) {
        $this->getEntityManager()->remove($drivingMission);
    }

    /**
     * @param \DateTime $day
     * @return DrivingMission[]
     */
    public function findDrivingMissionsForDay(\DateTime $day) {
        $qb = parent::createQueryBuilder('e');
        $qb->innerJoin('Tixi\CoreDomain\Dispo\DrivingOrder', 'o')
            ->where('e.id = o.drivingMission')
            ->andWhere('o.pickUpDate = :day')
            ->setParameter('day', $day->format('Y-m-d'));
        return $qb->getQuery()->getResult();
    }
}