<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface PostalCodeRepository extends CommonBaseRepository{

    public function store(PostalCode $postalCode);

    public function remove(PostalCode $postalCode);

}