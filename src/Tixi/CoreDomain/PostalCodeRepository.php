<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface PostalCodeRepository extends CommonBaseRepository{

    public function store(PostalCode $postalCode);

    public function remove(PostalCode $postalCode);

    /**
     * @param PostalCode $postalCode
     * @return PostalCode
     */
    public function storeAndGetPostalCode(PostalCode $postalCode);
}