<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:03
 */

namespace Tixi\App\Disposition;


use Tixi\App\AppBundle\Disposition\RideNode;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Vehicle;

/**
 * Interface DispositionManagement
 * @package Tixi\App\Disposition
 */
interface DispositionManagement {

    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @param $direction
     * @param $duration
     * @param $additionalTime
     * @return bool
     */
    public function checkFeasibility(\DateTime $day, \DateTime $time, $direction, $duration, $additionalTime);

    /**
     * runs routing algorithm to set optimized missions and orders for a shift
     * @param Shift $shift
     * @return mixed
     */
    public function getOptimizedPlanForShift(Shift $shift);

    /**
     * runs routing algorithm to set optimized missions and orders for a DayPlan
     * @return mixed
     */
    public function getOptimizedDayPlan();

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