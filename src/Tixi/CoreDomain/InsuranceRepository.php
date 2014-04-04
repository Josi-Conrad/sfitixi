<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface InsuranceRepository extends CommonBaseRepository{

    public function store(Insurance $insurance);

    public function remove(Insurance $insurance);

}