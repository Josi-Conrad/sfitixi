<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DrivingOrderRepository extends CommonBaseRepository {

    public function store(DrivingOrder $drivingOrder);

    public function remove(DrivingOrder $drivingOrder);

}