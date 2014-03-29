<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DrivingPoolRepository extends CommonBaseRepository {

    public function store(DrivingPool $drivingPool);

    public function remove(DrivingPool $drivingPool);

}