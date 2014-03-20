<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\HandicapRepository;

class HandicapRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements HandicapRepository {

    public function store(Handicap $handicap) {
        $this->getEntityManager()->persist($handicap);
    }

    public function remove(Handicap $handicap) {
        $this->getEntityManager()->remove($handicap);
    }
}