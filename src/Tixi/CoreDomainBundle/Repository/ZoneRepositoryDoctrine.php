<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZoneRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class ZoneRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class ZoneRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements ZoneRepository {
    /**
     * @param Zone $zone
     * @return mixed|void
     */
    public function store(Zone $zone) {
        $this->getEntityManager()->persist($zone);
    }

    /**
     * @param Zone $zone
     * @return mixed|void
     */
    public function remove(Zone $zone) {
        $this->getEntityManager()->remove($zone);
    }

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

    public function findUnclassifiedZone()
    {
        $qb = parent::createQueryBuilder('z');
        $qb->where('z.priority = :unclassifiedPriority')
            ->setParameter('unclassifiedPriority',Zone::UNCLASSIFIEDPRIORITY);
        return $qb->getQuery()->getOneOrNullResult();
    }
}