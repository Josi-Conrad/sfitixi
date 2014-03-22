<?php

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\City;
use Tixi\CoreDomain\CityRepository;

class CityRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements CityRepository {

    public function store(City $city) {
        $this->getEntityManager()->persist($city);
    }

    public function remove(City $city) {
        $this->getEntityManager()->remove($city);
    }
}