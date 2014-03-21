<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\PostalCode;
use Tixi\CoreDomain\PostalCodeRepository;

class PostalCodeRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PostalCodeRepository {

    public function store(PostalCode $postalCode) {
        $this->getEntityManager()->persist($postalCode);
    }

    public function remove(PostalCode $postalCode) {
        $this->getEntityManager()->remove($postalCode);
    }
}