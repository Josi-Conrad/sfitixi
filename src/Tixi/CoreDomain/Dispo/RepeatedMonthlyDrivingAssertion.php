<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:34
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedMonthlyDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="monthly")
 */
class RepeatedMonthlyDrivingAssertion extends RepeatedDrivingAssertion {
    /**
     * @ORM\Column(type="string", length=9)
     */
    protected $weekdayAsText;
    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $relativeWeekAsText;

    /**
     * @param Shift $shift
     * @return bool
     */
    public function matching(Shift $shift) {
        $startDate = $this->getAssertionPlan()->getAnchorDate();
        $endDate = $this->getAssertionPlan()->getEndingDate();
        /**@var $shiftDate \DateTime */
        $shiftDate = $shift->getDate();
        $checkDate = clone $shiftDate;
        $checkDate->modify($this->getRelativeWeekAsText() . ' ' . $this->getWeekdayAsText() . ' of this month');

        if ($shiftDate >= $startDate && $shiftDate <= $endDate) {
            if ($shiftDate->format('Ymd') === $checkDate->format('Ymd')) {
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

        $checkDate = clone $dateTime;
        $checkDate->modify($this->getRelativeWeekAsText() . ' ' . $this->getWeekdayAsText() . ' of this month');

        if ($dateTime >= $startDate && $dateTime <= $endDate) {
            if ($dateTime->format('Ymd') === $checkDate->format('Ymd')) {
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
     * @param mixed $relativeWeekAsText
     */
    public function setRelativeWeekAsText($relativeWeekAsText) {
        $this->relativeWeekAsText = $relativeWeekAsText;
    }

    /**
     * @return mixed
     */
    public function getRelativeWeekAsText() {
        return $this->relativeWeekAsText;
    }

    /**
     * @param mixed $weekdayAsText
     */
    public function setWeekdayAsText($weekdayAsText) {
        $this->weekdayAsText = $weekdayAsText;
    }

    /**
     * @return mixed
     */
    public function getWeekdayAsText() {
        return $this->weekdayAsText;
    }


}