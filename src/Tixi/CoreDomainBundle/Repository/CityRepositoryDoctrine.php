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

    /**
     * @param City $city
     * @return City
     */
    public function storeAndGetCity(City $city) {
        $current = $this->findOneBy(array('name' => $city->getName()));
        if (empty($current)) {
            $this->getEntityManager()->persist($city);
            return $city;
        }
        return $current;
    }
}