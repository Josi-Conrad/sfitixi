<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 06.04.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Helper\WeekdayService;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedMonthlyDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;

/**
 * Class RepeatedDrivingAssertionAssembler
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class RepeatedDrivingAssertionAssembler {

    //injected by service container via setter method
    private $dateTimeService;

    /**
     * @param RepeatedDrivingAssertionRegisterDTO $dto
     * @return RepeatedDrivingAssertionPlan
     */
    public function repeatedRegisterDTOToNewDrivingAssertionPlan(RepeatedDrivingAssertionRegisterDTO $dto) {
        $drivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            $dto->memo, $dto->anchorDate, $dto->frequency, $dto->withHolidays, $dto->endDate);
        return $drivingAssertionPlan;
    }

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @param RepeatedDrivingAssertionRegisterDTO $dto
     * @return RepeatedDrivingAssertionPlan
     */
    public function repeatedRegisterDTOToDrivingAssertionPlan(RepeatedDrivingAssertionPlan $assertionPlan, RepeatedDrivingAssertionRegisterDTO $dto) {
        $endingDate = (null!==$dto->endDate) ? $dto->endDate : DateTimeService::getMaxDateTime();
        $assertionPlan->setMemo($dto->memo);
        $assertionPlan->setAnchorDate($dto->anchorDate);
        $assertionPlan->setEndingDate($endingDate);
        $assertionPlan->setFrequency($dto->frequency);
        $assertionPlan->setWithHolidays($dto->withHolidays);
        return $assertionPlan;
    }

    /**
     * @param RepeatedDrivingAssertionRegisterDTO $dto
     * @return ArrayCollection
     */
    public function repeatedRegisterDTOtoMonthlyDrivingAssertions(RepeatedDrivingAssertionRegisterDTO $dto) {
        $monthlyDrivingAssertions = new ArrayCollection();
        /** @var ShiftSelectionDTO $shiftSelectionDTO */
        foreach($dto->getMonthlyShiftSelections() as $shiftSelectionDTO) {
            $selectionIdArray = $this->explodeMonthlySelectionId($shiftSelectionDTO->getSelectionId());
            $monthlyDrivingAssertion = new RepeatedMonthlyDrivingAssertion();
            $monthlyDrivingAssertion->setRelativeWeekAsText($selectionIdArray['relativeWeek']);
            $monthlyDrivingAssertion->setWeekdayAsText($selectionIdArray['weekday']);
            $monthlyDrivingAssertion->setShiftTypes($shiftSelectionDTO->getShiftSelection());
            $monthlyDrivingAssertions->add($monthlyDrivingAssertion);
        }
        return $monthlyDrivingAssertions;
    }

    /**
     * @param RepeatedDrivingAssertionRegisterDTO $dto
     * @return ArrayCollection
     */
    public function repeatedRegisterDTOtoWeeklyDrivingAssertions(RepeatedDrivingAssertionRegisterDTO $dto) {
        $weeklyDrivingAssertions = new ArrayCollection();
        /** @var ShiftSelectionDTO $shiftSelectionDTO */
        foreach($dto->getWeeklyShiftSelections() as $shiftSelectionDTO) {
            $weekday = $this->explodeWeeklySelectionId($shiftSelectionDTO->getSelectionId());
            $weeklyDrivingAssertion = new RepeatedWeeklyDrivingAssertion();
            $weeklyDrivingAssertion->setWeekday(WeekdayService::$weekdayToNumericConverter[$weekday]);
            $weeklyDrivingAssertion->setShiftTypes($shiftSelectionDTO->getShiftSelection());
            $weeklyDrivingAssertions->add($weeklyDrivingAssertion);
        }
        return $weeklyDrivingAssertions;
    }

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return RepeatedDrivingAssertionRegisterDTO
     */
    public function toRepeatedRegisterDTO(RepeatedDrivingAssertionPlan $assertionPlan) {
        $assertionDTO = new RepeatedDrivingAssertionRegisterDTO();
        $assertionDTO->id = $assertionPlan->getId();
        $assertionDTO->memo = $assertionPlan->getMemo();
        $assertionDTO->anchorDate = $assertionPlan->getAnchorDate();
        $assertionDTO->endDate = ($assertionPlan->getEndingDate() != DateTimeService::getMaxDateTime()) ? $assertionPlan->getEndingDate() : null;
        $assertionDTO->frequency = $assertionPlan->getFrequency();
        $assertionDTO->withHolidays = $assertionPlan->getWithHolidays();
        if($assertionDTO->frequency === 'weekly') {
            /** @var RepeatedWeeklyDrivingAssertion $weeklyAssertion */
            foreach($assertionPlan->getRepeatedDrivingAssertions() as $weeklyAssertion) {
                $assertionDTO->weeklyDaysSelector[] = WeekdayService::$numericToWeekdayConverter[$weeklyAssertion->getWeekday()];
                $assertionDTO->weeklyShiftSelections->add(
                    $this->createShiftSelectionDTO(
                        'day',
                        WeekdayService::$numericToWeekdayConverter[$weeklyAssertion->getWeekday()],
                        $weeklyAssertion->getShiftTypes()->toArray()
                    )
                );
            }
        }else {
            /** @var RepeatedMonthlyDrivingAssertion $monthlyAssertion */
            foreach($assertionPlan->getRepeatedDrivingAssertions() as $monthlyAssertion) {
                if($monthlyAssertion->getRelativeWeekAsText()==='first') {
                    $assertionDTO->monthlyFirstWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='second') {
                    $assertionDTO->monthlySecondWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='third') {
                    $assertionDTO->monthlyThirdWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='fourth') {
                    $assertionDTO->monthlyFourthWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='last') {
                    $assertionDTO->monthlyLastWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }
                $assertionDTO->monthlyShiftSelections->add(
                    $this->createShiftSelectionDTO(
                        $monthlyAssertion->getRelativeWeekAsText(),
                        $monthlyAssertion->getWeekdayAsText(),
                        $monthlyAssertion->getShiftTypes()->toArray()
                    )
                );
            }
        }
        return $assertionDTO;
    }

    /**
     * @param $selectedOccurency
     * @param $selectedDay
     * @param array $shiftSelection
     * @return ShiftSelectionDTO
     */
    protected function createShiftSelectionDTO($selectedOccurency, $selectedDay, array $shiftSelection) {
        $shiftDTO = new ShiftSelectionDTO();
        $shiftDTO->selectionId = $selectedOccurency.'_'.$selectedDay;
        $shiftDTO->shiftSelection = new ArrayCollection($shiftSelection);
        return $shiftDTO;
    }

    /**
     * @param $selectionId
     * @return array
     */
    protected function explodeMonthlySelectionId($selectionId) {
        $explodedArray = explode('_', $selectionId);
        return array(
            'relativeWeek' => $explodedArray[0],
            'weekday' => $explodedArray[1]
        );
    }

    /**
     * @param $selectionId
     * @return mixed
     */
    protected function explodeWeeklySelectionId($selectionId) {
        $explodedArray = explode('_', $selectionId);
        return $explodedArray[1];
    }

    /**
     * @param $assertionPlans
     * @return array
     */
    public function assertionPlansToEmbeddedListDTOs($assertionPlans) {
        $dtoArray = array();
        foreach ($assertionPlans as $assertionPlan) {
            $dtoArray[] = $this->assertionPlanToEmbeddedListDTO($assertionPlan);
        }
        return $dtoArray;
    }

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return RepeatedDrivingAssertionEmbeddedListDTO
     */
    protected function assertionPlanToEmbeddedListDTO(RepeatedDrivingAssertionPlan $assertionPlan) {
        $dto = new RepeatedDrivingAssertionEmbeddedListDTO();
        $dto->id = $assertionPlan->getId();
        $dto->memo = $assertionPlan->getMemo();
        $dto->anchorDate = $assertionPlan->getAnchorDate()->format('d.m.Y');
        $dto->endDate = ($assertionPlan->getEndingDate()!=DateTimeService::getMaxDateTime()) ? $assertionPlan->getEndingDate()->format('d.m.Y') : 'repeateddrivingmission.validtillrecalled';
        $dto->frequency= $assertionPlan->getFrequency();
        return $dto;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }

} 