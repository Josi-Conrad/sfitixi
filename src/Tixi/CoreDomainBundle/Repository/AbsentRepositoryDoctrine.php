<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\AbsentRepository;

class AbsentRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements AbsentRepository {

    public function store(Absent $absent) {
        $this->getEntityManager()->persist($absent);
    }

    public function remove(Absent $absent) {
        $this->getEntityManager()->remove($absent);
    }
}