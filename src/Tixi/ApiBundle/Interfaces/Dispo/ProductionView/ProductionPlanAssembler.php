<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.05.14
 * Time: 22:50
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\ProductionView;


use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

class ProductionPlanAssembler {

    /** @var  DispositionManagement $dispoService */
    protected $dispoService;

    public function createDTOtoNewProductionPlan(ProductionPlanCreateDTO $createDTO, WorkingMonthRepository $workingMonthRepository) {
        try {
            $date = new \DateTime();
            $date->setDate($createDTO->year,$createDTO->month, 1);
        }catch (\Exception $e) {
            return null;
        }
        /** @var WorkingMonth $workingMonth */
        $workingMonth = $workingMonthRepository->findWorkingMonthByDate($date);
        if(null === $workingMonth) {
            $workingMonth = $this->dispoService->openWorkingMonth($createDTO->year, $createDTO->month);
        }
        if(null !== $workingMonth) {
            $workingMonth->setMemo($createDTO->memo);
        }
        return $workingMonth;
    }

    public function workingMonthToEditDTO(WorkingMonth $workingMonth) {
        $dto = new ProductionPlanEditDTO();
        $dto->workingMonthId = $workingMonth->getId();
        $dto->dateString = $workingMonth->getDateString();
        $dto->memo = $workingMonth->getMemo();

        $workingDays = $workingMonth->getWorkingDays();

        /** @var WorkingDay $workingDay */
        foreach($workingDays as $workingDayIndex=>$workingDay) {
            $workingDayDTO = new ProductionViewWorkingDayDTO();
            $workingDayDTO->id = $workingDay->getId();
            $workingDayDTO->comment = $workingDay->getComment();
            $workingDayDTO->dateString = $workingDay->getDateString();
            $workingDayDTO->weekDayString = $workingDay->getWeekDayAsString();

            $workingShifts = $workingDay->getShiftsOrderedByStartTime();
            /** @var Shift $workingShift */
            foreach($workingShifts as $workingShift) {
                if($workingDayIndex===0) {
                    $dto->workingShiftsDisplayNames[] = $workingShift->getShiftType()->getName();
                }
                $workingShiftDTO = new ProductionViewWorkingShiftDTO();
                $workingShiftDTO->id = $workingShift->getId();
                $workingShiftDTO->amountOfDrivers = $workingShift->getAmountOfDrivers();
                $workingDayDTO->workingShifts[] = $workingShiftDTO;
            }
        $dto->workingDays[] = $workingDayDTO;
        }
        return $dto;
    }

    /**
     * @param ProductionPlanEditDTO $editDTO
     * @param WorkingMonth $workingMonth
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function editDtoToWorkingMonth(ProductionPlanEditDTO $editDTO, WorkingMonth $workingMonth) {
        $workingMonth->setMemo($editDTO->memo);
        foreach($workingMonth->getWorkingDays() as $workingDay) {
            /** @var ProductionViewWorkingDayDTO $formWorkingDay */
            $formWorkingDay = $editDTO->getWorkingDayPerId($workingDay->getId());
            $workingDay->setComment($formWorkingDay->comment);
            $workingShifts = $workingDay->getShifts();
            foreach($workingShifts as $workingShift) {
                /** @var ProductionViewWorkingShiftDTO $formWorkingShift */
                $formWorkingShift = $formWorkingDay->getWorkingShiftPerId($workingShift->getId());
                if($formWorkingShift->amountOfDrivers<0) {
                    throw new \InvalidArgumentException();
                }
                if($workingShift->getAmountOfDrivers()!==$formWorkingShift->amountOfDrivers) {
                    try {
                        $this->dispoService->processChangeInAmountOfDriversPerShift(
                            $workingShift, $workingShift->getAmountOfDrivers(), $formWorkingShift->amountOfDrivers);
                    }catch (\LogicException $e) {
                        throw new \LogicException($workingShift->getDate()->format('Y-m-d').', '.$workingShift->getShiftType()->getName());
                    }
                }
            }
            $editDTO->getWorkingDayPerId($workingDay->getId());
        }

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
     * @return ProductionPlanListDTO
     */
    public function workingMonthToListDTO(WorkingMonth $workingMonth) {
        $dto = new ProductionPlanListDTO();
        $dto->id = $workingMonth->getId();
        $dto->date = $workingMonth->getDateString();
        return $dto;
    }

    public function setDispoService(DispositionManagement $dispoService) {
        $this->dispoService = $dispoService;
    }

} 