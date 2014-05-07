<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:33
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="weekly")
 */
class RepeatedWeeklyDrivingAssertion extends RepeatedDrivingAssertion {
    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @param Shift $shift
     * @return mixed|void
     */
    public function matching(Shift $shift) {
        $startDate = $this->getAssertionPlan()->getAnchorDate();
        $endDate = $this->getAssertionPlan()->getEndingDate();
        /**@var $shiftDate \DateTime */
        $shiftDate = $shift->getDate();

        $wd = $shiftDate->format('N');

        if ($shiftDate >= $startDate && $shiftDate <= $endDate) {
            if ($wd == $this->getWeekday()) {
                if ($this->matchShiftType($shift->getShiftType())) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @param ShiftType $match
     * @return bool
     */
    protected function matchShiftType(ShiftType $match) {
        foreach ($this->getShiftTypes() as $shiftType) {
            if ($shiftType->getId() == $match->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $weekday
     */
    public function setWeekday($weekday) {
        $this->weekday = $weekday;
    }

    /**
     * @return integer
     */
    public function getWeekday() {
        return $this->weekday;
    }


}