<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\InsuranceRepository;

class InsuranceRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements InsuranceRepository {

    public function store(Insurance $insurance) {
        $this->getEntityManager()->persist($insurance);
    }

    public function remove(Insurance $insurance) {
        $this->getEntityManager()->remove($insurance);
    }
}