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

class RepeatedDrivingAssertionAssembler {

    public function repeatedRegisterDTOToNewDrivingAssertionPlan(RepeatedDrivingAssertionRegisterDTO $dto) {
        $drivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            $dto->anchorDate, $dto->frequency, $dto->withHolidays, $dto->endDate);
        return $drivingAssertionPlan;
    }


    public function repeatedRegisterDTOtoMonthlyDrivingAssertions(RepeatedDrivingAssertionRegisterDTO $dto) {
        $monthlyDrivingAssertions = new ArrayCollection();
        /** @var ShiftSelectionDTO $shiftSelectionDTO */
        foreach($dto->getMonthlyShiftSelections() as $shiftSelectionDTO) {
            $selectionIdArray = $this->explodeSelectionId($shiftSelectionDTO->getSelectionId());
            $monthlyDrivingAssertion = new RepeatedMonthlyDrivingAssertion();
            $monthlyDrivingAssertion->setRelativeWeekAsText($selectionIdArray['relativeWeek']);
            $monthlyDrivingAssertion->setWeekdayAsText($selectionIdArray['weekday']);
            $monthlyDrivingAssertion->setShiftTypes($shiftSelectionDTO->getShiftSelection());
            $monthlyDrivingAssertions->add($monthlyDrivingAssertion);
        }
        return $monthlyDrivingAssertions;
    }

    protected function explodeSelectionId($selectionId) {
        $explodedArray = explode('_', $selectionId);
        return array(
            'relativeWeek' => $explodedArray[0],
            'weekday' => $explodedArray[1]
        );
    }

} 