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
     * @return bool
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
     * @param \DateTime $dateTime
     * @return bool
     */
    public function matchingDateTime(\DateTime $dateTime) {
        $startDate = $this->getAssertionPlan()->getAnchorDate();
        $endDate = $this->getAssertionPlan()->getEndingDate();

        $wd = $dateTime->format('N');
        if ($dateTime >= $startDate && $dateTime <= $endDate) {
            if ($wd == $this->getWeekday()) {
                foreach ($this->getShiftTypes() as $shiftType) {
                    if ($shiftType->isResponsibleForTime($dateTime)) {
                        return true;
                    }
                }
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