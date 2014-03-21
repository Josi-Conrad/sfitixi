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

    /**
     * @param Country $country
     * @return Country
     */
    public function storeAndGetCountry(Country $country) {
        $current = $this->findOneBy(array('name' => $country->getName()));
        if (empty($current)) {
            $this->getEntityManager()->persist($country);
            return $country;
        }
        return $current;
    }
}