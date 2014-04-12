<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIRepository;

/**
 * Class POIRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class POIRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements POIRepository {
    /**
     * @param POI $poi
     * @return mixed|void
     */
    public function store(POI $poi) {
        $this->getEntityManager()->persist($poi);
    }

    /**
     * @param POI $poi
     * @return mixed|void
     */
    public function remove(POI $poi) {
        $this->getEntityManager()->remove($poi);
    }
}