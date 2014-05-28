<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:03
 */

namespace Tixi\App\Disposition;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Vehicle;

/**
 * Interface DispositionManagement
 * @package Tixi\App\Disposition
 */
interface DispositionManagement {

    public function openWorkingMonth($year, $month);

    public function processChangeInAmountOfDriversPerShift(Shift $shift, $oldAmount, $newAmount);

    public function createDrivingAssertionsFromMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan);

    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @return Shift
     */
    public function getResponsibleShiftForDayAndTime(\DateTime $day, \DateTime $time);

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