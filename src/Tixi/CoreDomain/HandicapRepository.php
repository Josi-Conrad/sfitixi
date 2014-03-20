<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface HandicapRepository extends CommonBaseRepository{

    public function store(Handicap $handicap);

    public function remove(Handicap $handicap);

}