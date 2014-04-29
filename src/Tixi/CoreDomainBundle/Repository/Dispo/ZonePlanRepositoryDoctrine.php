<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\ZonePlan;
use Tixi\CoreDomain\Dispo\ZonePlanRepository;
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
     * @param ZonePlan $zonePlan
     * @return mixed|void
     */
    public function remove(ZonePlan $zonePlan) {
        $this->getEntityManager()->remove($zonePlan);
    }
}