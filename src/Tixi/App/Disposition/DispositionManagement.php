<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:03
 */

namespace Tixi\App\Disposition;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Vehicle;

/**
 * Interface DispositionManagement
 * @package Tixi\App\Disposition
 */
interface DispositionManagement {

    /**
     * Creates all the needed entities for a new working month.
     * @param $year
     * @param $month
     * @return array|null|WorkingMonth
     */
    public function openWorkingMonth($year, $month);

    /**
     * Creates or removes working pools if more or less drivers are needed for a given shift
     * @param Shift $shift
     * @param $oldAmount
     * @param $newAmount
     * @return mixed
     */
    public function processChangeInAmountOfDriversPerShift(Shift $shift, $oldAmount, $newAmount);

    /**
     * @param MonthlyPlanEditDTO $monthlyPlan
     * @return mixed
     */
    public function createDrivingAssertionsFromMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan);

    /**
     * @param \DateTime $dayTime
     * @return Shift
     */
    public function getResponsibleShiftForDayAndTime(\DateTime $dayTime);

    /**
     * @param Shift $shift
     * @return DrivingMission[]
     */
    public function getDrivingMissionsInShift(Shift $shift);

    /**
     * @param \DateTime $day
     * @return Vehicle[]
     */
    public function getAvailableVehiclesForDay(\DateTime $day);

}