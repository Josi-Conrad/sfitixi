<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface CountryRepository extends CommonBaseRepository{

    public function store(Country $country);

    public function remove(Country $country);

    /**
     * @param Country $country
     * @return Country
     */
    public function storeAndGetCountry(Country $country);
    
}