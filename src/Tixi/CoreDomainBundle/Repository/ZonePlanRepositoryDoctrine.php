<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZonePlan;
use Tixi\CoreDomain\ZonePlanRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ZonePlanRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ZonePlanRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements ZonePlanRepository {
    /**
     * @param ZonePlan $zonePlan
     * @return mixed|void
     */
    public function store(ZonePlan $zonePlan) {
        $this->getEntityManager()->persist($zonePlan);
    }

    /**
     * @param Zone $zone
     * @return mixed
     */
    public function getAmountByZone(Zone $zone) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.zone = :zone');
        $qb->setParameter('zone', $zone);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param ZonePlan $zonePlan
     * @return mixed|void
     */
    public function remove(ZonePlan $zonePlan) {
        $this->getEntityManager()->remove($zonePlan);
    }
}