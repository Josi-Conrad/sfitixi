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

interface DrivingAssertionManagement {

    public function handleNewRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan);

    public function handleChangeInRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan);

    public function createAllDrivingAssertionsForNewMonthlyPlan(WorkingMonth $workingMonth);

    public function handleNewOrChangedAbsent(Driver $driver);

    public function handleMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan);

} 