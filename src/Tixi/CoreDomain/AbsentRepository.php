<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface AbsentRepository
 * @package Tixi\CoreDomain
 */
interface AbsentRepository extends CommonBaseRepository{
    /**
     * @param Absent $absent
     * @return mixed
     */
    public function store(Absent $absent);

    /**
     * @param Absent $absent
     * @return mixed
     */
    public function remove(Absent $absent);

}