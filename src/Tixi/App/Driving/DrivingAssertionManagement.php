<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 17:38
 */

namespace Tixi\App\Driving;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\WorkingMonth;

interface DrivingAssertionManagement {

    public function handleNewRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertion);

    public function handleChangeInRepeatedDrivingAssertion(RepeatedDrivingAssertion $repeatedDrivingAssertion);

    public function createAllDrivingAssertionsForNewMonthlyPlan(WorkingMonth $workingMonth);

    public function handleNewOrChangedAbsent(Absent $absent);

    public function handleMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan);

} 