<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface AbsentRepository extends CommonBaseRepository{

    public function store(Absent $absent);

    public function remove(Absent $absent);

}