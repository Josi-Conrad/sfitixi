<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\InsuranceRepository;

/**
 * Class InsuranceRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class InsuranceRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements InsuranceRepository {
    /**
     * @param Insurance $insurance
     * @return mixed|void
     */
    public function store(Insurance $insurance) {
        $this->getEntityManager()->persist($insurance);
    }

    /**
     * @param Insurance $insurance
     * @return mixed|void
     */
    public function remove(Insurance $insurance) {
        $this->getEntityManager()->remove($insurance);
    }
}