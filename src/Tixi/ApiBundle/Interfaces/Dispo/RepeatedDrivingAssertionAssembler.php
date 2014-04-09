<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 06.04.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedMonthlyDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;

class RepeatedDrivingAssertionAssembler {

    /**
     * @var array
     * php dayname -> ISO-8601 numeric representation of the day of the week
     */
    protected $weekdayToNumericConverter = array(
        'Monday'=>1,
        'Tuesday'=>2,
        'Wednesday'=>3,
        'Thursday'=>4,
        'Friday'=>5,
        'Saturday'=>6,
        'Sunday'=>7,
    );

    /**
     * @var array
     * ISO-8601 numeric representation of the day of the week -> php dayname
     */
    protected $numericToWeekdayConverter = array(
        1=>'Monday',
        2=>'Tuesday',
        3=>'Wednesday',
        4=>'Thursday',
        5=>'Friday',
        6=>'Saturday',
        7=>'Sunday'
    );

    public function repeatedRegisterDTOToNewDrivingAssertionPlan(RepeatedDrivingAssertionRegisterDTO $dto) {
        $drivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            $dto->memo, $dto->anchorDate, $dto->frequency, $dto->withHolidays, $dto->endDate);
        return $drivingAssertionPlan;
    }

    public function repeatedRegisterDTOToDrivingAssertionPlan(RepeatedDrivingAssertionPlan $assertionPlan, RepeatedDrivingAssertionRegisterDTO $dto) {
        $assertionPlan->setMemo($dto->memo);
        $assertionPlan->setAnchorDate($dto->anchorDate);
        $assertionPlan->setEndingDate($dto->endDate);
        $assertionPlan->setFrequency($dto->frequency);
        $assertionPlan->setWithHolidays($dto->withHolidays);
        return $assertionPlan;
    }


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

    public function repeatedRegisterDTOtoWeeklyDrivingAssertions(RepeatedDrivingAssertionRegisterDTO $dto) {
        $weeklyDrivingAssertions = new ArrayCollection();
        /** @var ShiftSelectionDTO $shiftSelectionDTO */
        foreach($dto->getWeeklyShiftSelections() as $shiftSelectionDTO) {
            $weekday = $this->explodeWeeklySelectionId($shiftSelectionDTO->getSelectionId());
            $weeklyDrivingAssertion = new RepeatedWeeklyDrivingAssertion();
            $weeklyDrivingAssertion->setWeekday($this->weekdayToNumericConverter[$weekday]);
            $weeklyDrivingAssertion->setShiftTypes($shiftSelectionDTO->getShiftSelection());
            $weeklyDrivingAssertions->add($weeklyDrivingAssertion);
        }
        return $weeklyDrivingAssertions;
    }

    public function toRepeatedRegisterDTO(RepeatedDrivingAssertionPlan $assertionPlan) {
        $assertionDTO = new RepeatedDrivingAssertionRegisterDTO();
        $assertionDTO->id = $assertionPlan->getId();
        $assertionDTO->memo = $assertionPlan->getMemo();
        $assertionDTO->anchorDate = $assertionPlan->getAnchorDate();
        $assertionDTO->endDate = $assertionPlan->getEndingDate();
        $assertionDTO->frequency = $assertionPlan->getFrequency();
        $assertionDTO->withHolidays = $assertionPlan->getWithHolidays();
        if($assertionDTO->frequency === 'weekly') {
            /** @var RepeatedWeeklyDrivingAssertion $weeklyAssertion */
            foreach($assertionPlan->getRepeatedDrivingAssertions() as $weeklyAssertion) {
                $assertionDTO->weeklyDaysSelector[] = $this->numericToWeekdayConverter[$weeklyAssertion->getWeekday()];
                $assertionDTO->weeklyShiftSelections->add(
                    $this->createShiftSelectionDTO(
                        'Day',
                        $this->numericToWeekdayConverter[$weeklyAssertion->getWeekday()],
                        $weeklyAssertion->getShiftTypes()->toArray()
                    )
                );
            }
        }else {
            /** @var RepeatedMonthlyDrivingAssertion $monthlyAssertion */
            foreach($assertionPlan->getRepeatedDrivingAssertions() as $monthlyAssertion) {
                if($monthlyAssertion->getRelativeWeekAsText()==='First') {
                    $assertionDTO->monthlyFirstWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='Second') {
                    $assertionDTO->monthlySecondWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='Third') {
                    $assertionDTO->monthlyThirdWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='Fourth') {
                    $assertionDTO->monthlyFourthWeeklySelector[]=$monthlyAssertion->getWeekdayAsText();
                }else if($monthlyAssertion->getRelativeWeekAsText()==='Last') {
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

    protected function createShiftSelectionDTO($selectedOccurency, $selectedDay, array $shiftSelection) {
        $shiftDTO = new ShiftSelectionDTO();
        $shiftDTO->selectionId = $selectedOccurency.'_'.$selectedDay;
        $shiftDTO->shiftSelection = new ArrayCollection($shiftSelection);
        return $shiftDTO;
    }

    protected function explodeMonthlySelectionId($selectionId) {
        $explodedArray = explode('_', $selectionId);
        return array(
            'relativeWeek' => $explodedArray[0],
            'weekday' => $explodedArray[1]
        );
    }

    protected function explodeWeeklySelectionId($selectionId) {
        $explodedArray = explode('_', $selectionId);
        return $explodedArray[1];
    }

} 