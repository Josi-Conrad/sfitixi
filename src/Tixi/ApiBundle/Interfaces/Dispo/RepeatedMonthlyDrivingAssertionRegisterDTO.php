<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:36
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;

class RepeatedMonthlyDrivingAssertionRegisterDTO {

    public $firstWeeklySelector;
    public $secondWeeklySelector;
    public $thirdWeeklySelector;
    public $fourthWeeklySelector;
    public $lastWeeklySelector;

    public $shiftSelections;

    public function __construct() {
        $this->shiftSelections = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getFirstWeeklySelector()
    {
        return $this->firstWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getFourthWeeklySelector()
    {
        return $this->fourthWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getLastWeeklySelector()
    {
        return $this->lastWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getSecondWeeklySelector()
    {
        return $this->secondWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getShiftSelections()
    {
        return $this->shiftSelections;
    }

    /**
     * @return mixed
     */
    public function getThirdWeeklySelector()
    {
        return $this->thirdWeeklySelector;
    }



} 