<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class WorkingDayRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class WorkingDayRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements WorkingDayRepository {
    /**
     * @param WorkingDay $workingDay
     */
    public function store(WorkingDay $workingDay) {
        $this->getEntityManager()->persist($workingDay);
    }

    /**
     * @param WorkingDay $workingDay
     */
    public function remove(WorkingDay $workingDay) {
        $this->getEntityManager()->remove($workingDay);
    }
}