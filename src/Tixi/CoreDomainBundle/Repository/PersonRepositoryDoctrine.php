<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Person;
use Tixi\CoreDomain\PersonCategory;
use Tixi\CoreDomain\PersonRepository;

/**
 * Class PersonRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class PersonRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements PersonRepository {
    /**
     * @param Person $person
     * @return mixed|void
     */
    public function store(Person $person) {
        $this->getEntityManager()->persist($person);
    }

    /**
     * @param Person $person
     * @return mixed|void
     */
    public function remove(Person $person) {
        $this->getEntityManager()->remove($person);
    }

    /**
     * @param PersonCategory $personCategory
     * @return mixed
     */
    public function getAmountByPersonCategory(PersonCategory $personCategory) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->innerJoin('e.personCategories', 'c', 'WITH', 'c = :personCategory');
        $qb->setParameter('personCategory', $personCategory);
        return $qb->getQuery()->getSingleScalarResult();
    }

}