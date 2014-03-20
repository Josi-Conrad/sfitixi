<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface POIRepository extends CommonBaseRepository{

    public function store(POI $poi);

    public function remove(POI $poi);
} 