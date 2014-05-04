<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface WorkingMonthRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface WorkingMonthRepository extends CommonBaseRepository {
    /**
     * @param WorkingMonth $workingMonth
     * @return mixed
     */
    public function store(WorkingMonth $workingMonth);

    /**
     * @param WorkingMonth $workingMonth
     * @return mixed
     */
    public function remove(WorkingMonth $workingMonth);

    /**
     * @param \DateTime $date
     * @return mixed
     */
    public function findWorkingMonthByDate(\DateTime $date);
}