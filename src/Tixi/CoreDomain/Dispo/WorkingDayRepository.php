<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface WorkingDayRepository extends CommonBaseRepository {

    public function store(WorkingDay $workingDay);

    public function remove(WorkingDay $workingDay);

}