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

    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name) {
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.isDeleted = 0')
            ->andWhere('s.name = :duplicateName')
            ->setParameter('duplicateName', $name);
        if ($qb->getQuery()->getResult()) {
            return true;
        } else {
            return false;
        }
    }
}