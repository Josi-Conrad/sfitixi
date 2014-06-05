<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface POIKeywordRepository
 * @package Tixi\CoreDomain
 */
interface POIKeywordRepository extends CommonBaseRepository{
    /**
     * @param POIKeyword $POIKeyword
     * @return mixed
     */
    public function store(POIKeyword $POIKeyword);

    /**
     * @param POIKeyword $POIKeyword
     * @return mixed
     */
    public function remove(POIKeyword $POIKeyword);
    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name);
} 