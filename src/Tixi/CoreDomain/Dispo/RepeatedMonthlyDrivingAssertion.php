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
     * @return mixed|void
     */
    public function matching(Shift $shift) {
        $startDate = $this->getAssertionPlan()->getAnchorDate();
        $endDate = $this->getAssertionPlan()->getEndingDate();
        $shiftDate = $shift->getDate();
        if ($shiftDate >= $startDate && $shiftDate <= $endDate) {
            //TODO check weekdays and shiftTypes
            return true;
        }

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