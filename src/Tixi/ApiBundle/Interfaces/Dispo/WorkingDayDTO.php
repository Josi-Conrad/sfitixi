<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:32
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Dispo\Shift;

/**
 * Class WorkingDayDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class WorkingDayDTO {

    public $workingDayId;
    public $workingDayDate;
    public $workingDayDateString;
    public $workingDayWeekDayString;
    public $workingDayComment;

    public $workingShifts;

    public function __construct() {
        $this->workingShifts = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWorkingShifts() {
        return $this->workingShifts;
    }

    /**
     * @param $id
     * @return WorkingShiftDTO
     */
    public function getWorkingShiftById($id) {
        /**@var $wsDTO WorkingShiftDTO */
        foreach ($this->workingShifts as $wsDTO) {
            if ($wsDTO->workingShiftId === $id) {
                return $wsDTO;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getWorkingDayComment() {
        return $this->workingDayComment;
    }

    /**
     * @return mixed
     */
    public function getWorkingDayDate() {
        return $this->workingDayDate;
    }

    /**
     * @return mixed
     */
    public function getWorkingDayId() {
        return $this->workingDayId;
    }

}