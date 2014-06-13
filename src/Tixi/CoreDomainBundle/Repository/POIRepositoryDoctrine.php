<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIKeyword;
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

    /**
     * @param POIKeyword $poiKeyword
     * @return mixed
     */
    public function getAmountByPOIKeyword(POIKeyword $poiKeyword)
    {
        $qb = parent::createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->join('e.keywords', 'k');
        $qb->where('k = :keyword');
        $qb->setParameter('keyword', $poiKeyword);
        return $qb->getQuery()->getSingleScalarResult();
    }
}