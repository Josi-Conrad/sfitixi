<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\HandicapRepository;

/**
 * Class HandicapRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class HandicapRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements HandicapRepository {
    /**
     * @param Handicap $handicap
     * @return mixed|void
     */
    public function store(Handicap $handicap) {
        $this->getEntityManager()->persist($handicap);
    }

    /**
     * @param Handicap $handicap
     * @return mixed|void
     */
    public function remove(Handicap $handicap) {
        $this->getEntityManager()->remove($handicap);
    }
}