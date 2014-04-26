<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface WorkingDayRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface WorkingDayRepository extends CommonBaseRepository {
    /**
     * @param WorkingDay $workingDay
     * @return mixed
     */
    public function store(WorkingDay $workingDay);

    /**
     * @param WorkingDay $workingDay
     * @return mixed
     */
    public function remove(WorkingDay $workingDay);

}