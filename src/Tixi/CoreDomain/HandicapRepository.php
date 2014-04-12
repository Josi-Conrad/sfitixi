<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface HandicapRepository
 * @package Tixi\CoreDomain
 */
interface HandicapRepository extends CommonBaseRepository{
    /**
     * @param Handicap $handicap
     * @return mixed
     */
    public function store(Handicap $handicap);

    /**
     * @param Handicap $handicap
     * @return mixed
     */
    public function remove(Handicap $handicap);

}