<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\POIKeyword;
use Tixi\Coredomain\POIKeywordRepository;

class POIKeywordRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements POIKeywordRepository {

    public function store(POIKeyword $POIKeyword) {
        $this->getEntityManager()->persist($POIKeyword);
    }

    public function remove(POIKeyword $POIKeyword) {
        $this->getEntityManager()->remove($POIKeyword);
    }
}