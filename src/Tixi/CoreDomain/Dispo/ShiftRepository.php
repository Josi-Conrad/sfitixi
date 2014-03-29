<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface ShiftRepository extends CommonBaseRepository {

    public function store(Shift $shift);

    public function remove(Shift $shift);

}