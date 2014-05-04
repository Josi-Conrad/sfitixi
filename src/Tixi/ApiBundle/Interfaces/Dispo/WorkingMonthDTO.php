<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:32
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Dispo\WorkingDay;

/**
 * Class WorkingMonthDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class WorkingMonthDTO {

    public $workingMonthId;

    public $workingMonthMemo;
    public $workingMonthDate;
    public $workingMonthStatus;
    public $workingMonthDateString;

    public $workingDays;
    public $workingShiftNames;

    public function __construct() {
        $this->workingDays = new ArrayCollection();
        $this->workingShiftNames = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWorkingShiftNames() {
        return $this->workingShiftNames;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWorkingDays() {
        return $this->workingDays;
    }

    /**
     * @param $id
     * @return WorkingDayDTO
     */
    public function getWorkingDayById($id) {
        /**@var $wdDTO WorkingDayDTO */
        foreach ($this->workingDays as $wdDTO) {
            if ($wdDTO->workingDayId == $id) {
                return $wdDTO;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getWorkingMonthDate() {
        return $this->workingMonthDate;
    }

    /**
     * @return mixed
     */
    public function getWorkingMonthDateString() {
        return $this->workingMonthDateString;
    }

    /**
     * @return mixed
     */
    public function getWorkingMonthId() {
        return $this->workingMonthId;
    }

    /**
     * @return mixed
     */
    public function getWorkingMonthMemo() {
        return $this->workingMonthMemo;
    }

    /**
     * @return mixed
     */
    public function getWorkingMonthStatus() {
        return $this->workingMonthStatus;
    }

}