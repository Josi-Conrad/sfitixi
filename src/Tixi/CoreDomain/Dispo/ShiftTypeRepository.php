<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface ShiftTypeRepository extends CommonBaseRepository {

    public function store(ShiftType $shiftType);

    public function remove(ShiftType $shiftType);

}