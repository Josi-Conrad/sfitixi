<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:36
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;

class RepeatedDrivingAssertionRegisterDTO {

    public $memo;
    public $anchorDate;
    public $endDate;
    public $frequency;
    public $withHolidays;

    //weekly part
    public $weeklyDaysSelector;
    public $weeklyShiftSelections;

    //monthly part
    public $monthlyFirstWeeklySelector;
    public $monthlySecondWeeklySelector;
    public $monthlyThirdWeeklySelector;
    public $monthlyFourthWeeklySelector;
    public $monthlyLastWeeklySelector;

    public $monthlyShiftSelections;

    public function __construct() {
        $this->monthlyShiftSelections = new ArrayCollection();
        $this->weeklyShiftSelections = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getMonthlyFirstWeeklySelector()
    {
        return $this->monthlyFirstWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getMonthlyFourthWeeklySelector()
    {
        return $this->monthlyFourthWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getMonthlyLastWeeklySelector()
    {
        return $this->monthlyLastWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getMonthlySecondWeeklySelector()
    {
        return $this->monthlySecondWeeklySelector;
    }

    /**
     * @return mixed
     */
    public function getMonthlyThirdWeeklySelector()
    {
        return $this->monthlyThirdWeeklySelector;
    }



    /**
     * @return mixed
     */
    public function getMonthlyShiftSelections()
    {
        return $this->monthlyShiftSelections;
    }

    /**
     * @return mixed
     */
    public function getWeeklyDaysSelector()
    {
        return $this->weeklyDaysSelector;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getWeeklyShiftSelections()
    {
        return $this->weeklyShiftSelections;
    }


} 