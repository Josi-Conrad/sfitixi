<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\AddressRepository;

/**
 * Class AddressRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class AddressRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements AddressRepository {
    /**
     * @param Address $address
     * @return mixed|void
     */
    public function store(Address $address) {
        $this->getEntityManager()->persist($address);
    }

    /**
     * @param Address $address
     * @return mixed|void
     */
    public function remove(Address $address) {
        $this->getEntityManager()->remove($address);
    }

    /**
     * @return Address[]
     */
    public function findAddressesWithoutCoordinates() {
        $qb = parent::createQueryBuilder('a');
        $qb->where('a.lat = 0');
        return $qb->getQuery()->getResult();
    }
}