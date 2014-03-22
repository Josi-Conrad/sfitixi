<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Country;
use Tixi\CoreDomain\CountryRepository;

class CountryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements CountryRepository {

    public function store(Country $country) {
        $this->getEntityManager()->persist($country);
    }

    public function remove(Country $country) {
        $this->getEntityManager()->remove($country);
    }
}