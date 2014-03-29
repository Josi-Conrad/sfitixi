<?php

namespace Tixi\CoreDomainBundle\Repository\Dispo;

use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class WorkingDayRepositoryDoctrine  extends CommonBaseRepositoryDoctrine implements WorkingDayRepository {

    public function store(WorkingDay $workingDay) {
        $this->getEntityManager()->persist($workingDay);
    }

    public function remove(WorkingDay $workingDay) {
        $this->getEntityManager()->remove($workingDay);
    }
}