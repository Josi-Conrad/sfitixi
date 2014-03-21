<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface CityRepository extends CommonBaseRepository{

    public function store(City $city);

    public function remove(City $city);

    /**
     * @param City $city
     * @return City
     */
    public function storeAndGetCity(City $city);
}