<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DriverCategoryRepository extends CommonBaseRepository{

    public function store(DriverCategory $driverCategory);

    public function remove(DriverCategory $driverCategory);

}