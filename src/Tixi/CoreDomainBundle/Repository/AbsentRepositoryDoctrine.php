<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\AbsentRepository;

/**
 * Class AbsentRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class AbsentRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements AbsentRepository {
    /**
     * @param Absent $absent
     * @return mixed|void
     */
    public function store(Absent $absent) {
        $this->getEntityManager()->persist($absent);
    }

    /**
     * @param Absent $absent
     * @return mixed|void
     */
    public function remove(Absent $absent) {
        $this->getEntityManager()->remove($absent);
    }
}