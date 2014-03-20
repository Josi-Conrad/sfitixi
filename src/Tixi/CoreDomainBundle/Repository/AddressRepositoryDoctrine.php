<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\AddressRepository;

class AddressRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements AddressRepository {

    public function store(Address $address) {
        $this->getEntityManager()->persist($address);
    }

    public function remove(Address $address) {
        $this->getEntityManager()->remove($address);
    }
}