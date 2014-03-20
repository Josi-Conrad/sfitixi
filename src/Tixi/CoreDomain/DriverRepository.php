<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DriverRepository extends CommonBaseRepository{

    public function store(Driver $driver);

    public function remove(Driver $driver);
} 