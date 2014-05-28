<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:49
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;

use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingDay;

/**
 * Class MonthlyPlanAssembler
 * @package Tixi\ApiBundle\Interfaces\Dispo\MonthlyView
 */
class MonthlyPlanAssembler {

    public function workingDayToEditDTO(WorkingDay $workingDay, $workingMonthId) {
        $dto = new MonthlyPlanEditDTO();
        $dto->workingMonthId = $workingMonthId;
        $dto->workingMonthDateString = $workingDay->getWorkingMonth()->getDateString();
        $dto->workingDayWeekdayString = $workingDay->getWeekDayAsString();
        $dto->workingDayDateString = $workingDay->getDateString();
        $shifts = $workingDay->getShiftsOrderedByStartTime();
        /** @var Shift $shift */
        foreach($shifts as $shift) {
            $driversPerShiftDTO = new MonthlyPlanDriversPerShiftDTO();
            $driversPerShiftDTO->shiftDisplayName = $shift->getShiftType()->getName();
            for($i=0;$i<$shift->getAmountOfMissingDrivers();$i++) {
                $driversPerShiftDTO->newDrivers[] = new MonthlyPlanDrivingAssertionDTO();
            }
            $dto->shifts[] = $driversPerShiftDTO;
        }
        return $dto;
    }

}