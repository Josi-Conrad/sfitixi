<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface InsuranceRepository
 * @package Tixi\CoreDomain
 */
interface InsuranceRepository extends CommonBaseRepository{
    /**
     * @param Insurance $insurance
     * @return mixed
     */
    public function store(Insurance $insurance);

    /**
     * @param Insurance $insurance
     * @return mixed
     */
    public function remove(Insurance $insurance);

}