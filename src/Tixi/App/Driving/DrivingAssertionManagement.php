<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 17:38
 */

namespace Tixi\App\Driving;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Driver;

/**
 * responsible for DrivingAssertion change management
 * Interface DrivingAssertionManagement
 * @package Tixi\App\Driving
 */
interface DrivingAssertionManagement {
    /**
     * @param RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan
     * @return mixed
     */
    public function handleNewRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan);

    /**
     * @param RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan
     * @return mixed
     */
    public function handleChangeInRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan);

    /**
     * @param WorkingMonth $workingMonth
     * @return mixed
     */
    public function createAllDrivingAssertionsForNewMonthlyPlan(WorkingMonth $workingMonth);

    /**
     * @param Driver $driver
     * @return mixed
     */
    public function handleNewOrChangedAbsent(Driver $driver);

    /**
     * @param MonthlyPlanEditDTO $monthlyPlan
     * @return mixed
     */
    public function handleMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan);

} 