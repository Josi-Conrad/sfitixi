<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:49
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;

use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;

/**
 * Class MonthlyPlanAssembler
 * @package Tixi\ApiBundle\Interfaces\Dispo\MonthlyView
 */
class MonthlyPlanAssembler {

    /** @var  DispositionManagement $dispoService */
    protected $dispoService;

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
            $driversPerShiftDTO->shiftId = $shift->getId();
            $driversPerShiftDTO->shiftDisplayName = $shift->getShiftType()->getName();
            $assignedDrivers = $shift->getAssignedDrivers();
            $driversPerShiftDTO->driversWithAssertion = $assignedDrivers;
            //for the open slots we ask for drivers
            for($i=0;$i<$shift->getAmountOfMissingDrivers();$i++) {
                $driversPerShiftDTO->newDrivers[] = new MonthlyPlanDrivingAssertionDTO();
            }

            $dto->shifts[] = $driversPerShiftDTO;
        }
        return $dto;
    }

    public function editDTOtoWorkingDay(MonthlyPlanEditDTO $editDTO) {
        $this->dispoService->createDrivingAssertionsFromMonthlyPlan($editDTO);
    }

    /**
     * @param $workingMonths
     * @return array
     */
    public function workingMonthsToListDTOs($workingMonths) {
        $dtoArray = array();
        foreach ($workingMonths as $workingMonth) {
            $dtoArray[] = $this->workingMonthToListDTO($workingMonth);
        }
        return $dtoArray;
    }

    public function workingMonthToListDTO(WorkingMonth $workingMonth) {
        $dto = new MonthlyPlanListDTO();
        $dto->id = $workingMonth->getId();
        $dto->date = $workingMonth->getDateString();
        $dto->status = $workingMonth->getStatusAsTransString();
        return $dto;
    }

    /**
     * @param $workingDays
     * @return array
     */
    public function workingMonthsTooWorkingDayListDTOs($workingDays) {
        $dtoArray = array();
        foreach ($workingDays as $workingDay) {
            $dtoArray[] = $this->workingMonthToWorkingDayListDTO($workingDay);
        }
        return $dtoArray;
    }

    public function workingMonthToWorkingDayListDTO(WorkingDay $workingDay) {
        $dto = new MonthlyPlanWorkingDayListDTO();
        $dto->id = $workingDay->getId();
        $dto->dateString = $workingDay->getDateString();
        $dto->weekDayString = $workingDay->getWeekDayAsString();
        return $dto;
    }

    public function setDispoService(DispositionManagement $dispoService) {
        $this->dispoService = $dispoService;
    }



}