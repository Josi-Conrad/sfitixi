<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface POIKeywordRepository extends CommonBaseRepository{

    public function store(POIKeyword $POIKeyword);

    public function remove(POIKeyword $POIKeyword);
} 