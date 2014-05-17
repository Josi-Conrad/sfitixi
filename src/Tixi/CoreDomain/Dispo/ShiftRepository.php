<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface ShiftRepository extends CommonBaseRepository {
    /**
     * @param Shift $shift
     * @return mixed
     */
    public function store(Shift $shift);

    /**
     * @param Shift $shift
     * @return mixed
     */
    public function remove(Shift $shift);

    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function getAmountByShiftType(ShiftType $shiftType);

    /**
     * @param \DateTime $day
     * @return Shift[]
     */
    public function findShiftsForDay(\DateTime $day);

}