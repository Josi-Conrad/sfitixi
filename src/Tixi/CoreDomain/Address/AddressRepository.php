<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface AddressRepository extends CommonBaseRepository{

    public function store(Address $address);

    public function remove(Address $address);

}