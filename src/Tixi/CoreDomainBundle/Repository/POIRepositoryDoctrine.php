<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIRepository;

class POIRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements POIRepository {

    public function store(POI $poi) {
        $this->getEntityManager()->persist($poi);
    }

    public function remove(POI $poi) {
        $this->getEntityManager()->remove($poi);
    }
}