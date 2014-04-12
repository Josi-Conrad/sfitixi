<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\POIKeyword;
use Tixi\CoreDomain\POIKeywordRepository;

/**
 * Class POIKeywordRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class POIKeywordRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements POIKeywordRepository {
    /**
     * @param POIKeyword $POIKeyword
     * @return mixed|void
     */
    public function store(POIKeyword $POIKeyword) {
        $this->getEntityManager()->persist($POIKeyword);
    }

    /**
     * @param POIKeyword $POIKeyword
     * @return mixed|void
     */
    public function remove(POIKeyword $POIKeyword) {
        $this->getEntityManager()->remove($POIKeyword);
    }
}