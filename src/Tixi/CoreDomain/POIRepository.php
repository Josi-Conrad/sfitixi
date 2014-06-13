<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface POIRepository
 * @package Tixi\CoreDomain
 */
interface POIRepository extends CommonBaseRepository{
    /**
     * @param POI $poi
     * @return mixed
     */
    public function store(POI $poi);

    /**
     * @param POI $poi
     * @return mixed
     */
    public function remove(POI $poi);

    /**
     * @param POIKeyword $poiKeyword
     * @return mixed
     */
    public function getAmountByPOIKeyword(POIKeyword $poiKeyword);
} 