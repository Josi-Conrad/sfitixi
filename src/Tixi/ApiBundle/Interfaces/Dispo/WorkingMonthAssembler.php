<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

/**
 * Class WorkingMonthAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class WorkingMonthAssembler {

    /**
     * @var array
     * ISO-8601 numeric representation of the day of the week -> php dayname
     */
    protected $numericToWeekdayConverter = array(
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday'
    );

    /**
     * @param WorkingMonth $workingMonth
     * @return WorkingMonthDTO
     */
    public function workingMonthToDTO(WorkingMonth $workingMonth) {
        $dto = new WorkingMonthDTO();
        $dto->workingMonthId = $workingMonth->getId();
        $dto->workingMonthDate = $workingMonth->getDate();
        $dto->workingMonthMemo = $workingMonth->getMemo();
        $dto->workingMonthStatus = $workingMonth->getStatus();
        $dto->workingMonthDateString = $workingMonth->getDate()->format('m - Y');

        /**@var $shift \Tixi\CoreDomain\Dispo\Shift */
        foreach ($workingMonth->getWorkingDays()->first()->getShifts() as $shift) {
            $shiftNameDTO = new WorkingShiftNameDTO();
            $shiftNameDTO->workingShiftName = $shift->getShiftType()->getName();
            $dto->getWorkingShiftNames()->add($shiftNameDTO);
        }

        /**@var $workingDay \Tixi\CoreDomain\Dispo\WorkingDay */
        foreach ($workingMonth->getWorkingDays() as $workingDay) {
            $workingDayDTO = new WorkingDayDTO();
            $workingDayDTO->workingDayId = $workingDay->getId();
            $workingDayDTO->workingDayComment = $workingDay->getComment();
            $workingDayDTO->workingDayDate = $workingDay->getDate();
            $workingDayDTO->workingDayDateString = $workingDay->getDate()->format('d.m.Y');
            $workingDayDTO->workingDayWeekDayString =
                $this->numericToWeekdayConverter[$workingDay->getDate()->format('N')] . '.name';

            /**@var $shift \Tixi\CoreDomain\Dispo\Shift */
            foreach ($workingDay->getShifts() as $shift) {
                $shiftDTO = new WorkingShiftDTO();
                $shiftDTO->workingShiftId = $shift->getId();
                $shiftDTO->workingShiftAmountOfDrivers = $shift->getAmountOfDrivers();
                $workingDayDTO->workingShifts->add($shiftDTO);
            }
            $dto->workingDays->add($workingDayDTO);
        }
        return $dto;
    }

    /**
     * Saves all changed properties in WorkingMonth, WorkingDay and WorkingShift from the corresponding DTOs
     * @param WorkingMonthDTO $workingMonthDTO
     * @param WorkingMonth $workingMonth
     * @param WorkingMonthRepository $wmRepo
     * @param WorkingDayRepository $wdRepo
     * @param ShiftRepository $shiftRepo
     * @return WorkingMonth
     */
    public function dtoToWorkingMonth(WorkingMonthDTO $workingMonthDTO, WorkingMonth $workingMonth,
                                      WorkingMonthRepository $wmRepo, WorkingDayRepository $wdRepo, ShiftRepository $shiftRepo) {
        $workingMonth->setMemo($workingMonthDTO->getWorkingMonthMemo());
        /**@var $workingDay \Tixi\CoreDomain\Dispo\WorkingDay */
        foreach ($workingMonth->getWorkingDays() as $workingDay) {
            $wdDTO = $workingMonthDTO->getWorkingDayById($workingDay->getId());
            $workingDay->setComment($wdDTO->getWorkingDayComment());
            /**@var $shift \Tixi\CoreDomain\Dispo\Shift */
            foreach ($workingDay->getShifts() as $shift) {
                $wsDTO = $wdDTO->getWorkingShiftById($shift->getId());
                $shift->setAmountOfDrivers($wsDTO->getWorkingShiftAmountOfDrivers());
                $shiftRepo->store($shift);
            }
            $wdRepo->store($workingDay);
        }
        $wmRepo->store($workingMonth);
        return $workingMonth;
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

    /**
     * @param WorkingMonth $workingMonth
     * @return WorkingMonthListDTO
     */
    public function workingMonthToListDTO(WorkingMonth $workingMonth) {
        $dto = new WorkingMonthListDTO();
        $dto->id = $workingMonth->getId();
        $dto->date = $workingMonth->getDate()->format('m - Y');
        $dto->status = $workingMonth->getStatus();
        $dto->memo = $workingMonth->getMemo();
        return $dto;
    }
}