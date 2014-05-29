<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.05.14
 * Time: 13:27
 */

namespace Tixi\App\AppBundle\Driving;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\App\Driving\DrivingAssertionManagement;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\BankHolidayRepository;
use Tixi\CoreDomain\Dispo\DrivingAssertion;
use Tixi\CoreDomain\Dispo\DrivingAssertionRepository;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlanRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverRepository;

class DrivingAssertionManagementImpl extends ContainerAware implements DrivingAssertionManagement{

    public function handleNewRepeatedDrivingAssertion(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan)
    {
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->container->get('workingmonth_repository');

        $nextWorkingMonths = $workingMonthRepository->findNextActiveWorkingMonths();
        /** @var WorkingMonth $workingMonth */
        foreach($nextWorkingMonths as $workingMonth) {
            $this->handleRepeatedDrivingAssertionsForWorkingMonth($repeatedDrivingAssertionPlan, $workingMonth);
        }
    }

    public function handleRepeatedDrivingAssertionsForWorkingMonth(
        RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan, WorkingMonth $workingMonth) {

        /** @var DrivingAssertionRepository $drivingAssertionRepository */
        $drivingAssertionRepository = $this->container->get('drivingassertion_repository');
        /** @var BankHolidayRepository $bankHolidayRepository */
        $bankHolidayRepository = $this->container->get('bankholiday_repository');

        $driver = $repeatedDrivingAssertionPlan->getDriver();
        $workingDays = $workingMonth->getWorkingDays();
        /** @var WorkingDay $workingDay */
        foreach($workingDays as $workingDay) {
            $shifts = $workingDay->getShifts();
            $isBankHoliday = $bankHolidayRepository->checkIfWorkingDayIsBankHoliday($workingDay);
            foreach($shifts as $shift) {
                if($driver->isAvailableOn($shift, $isBankHoliday)) {
                    if(!$driver->hasDrivingAssertionForShift($shift)) {
                        $drivingAssertion = DrivingAssertion::registerDrivingAssertion($driver, $shift);
                        $drivingAssertion->assignedRepeatedDrivingAssertionPlan($repeatedDrivingAssertionPlan);
                        $repeatedDrivingAssertionPlan->assigneDrivingAssertion($drivingAssertion);
                        $drivingAssertionRepository->store($drivingAssertion);
                    }
                }
            }
        }

    }

    public function handleChangeInRepeatedDrivingAssertion(RepeatedDrivingAssertion $repeatedDrivingAssertion)
    {
        // TODO: Implement handleChangeInRepeatedDrivingAssertion() method.
    }

    public function createAllDrivingAssertionsForNewMonthlyPlan(WorkingMonth $workingMonth)
    {
        /** @var RepeatedDrivingAssertionPlanRepository $repeatedDrivingAssertionPlanRepository */
        $repeatedDrivingAssertionPlanRepository = $this->container->get('repeateddrivingassertionplan_repository');

        $repeatedPlansInRange = $repeatedDrivingAssertionPlanRepository->findActivePlansInRangeOfWorkingMonth($workingMonth);
        /** @var RepeatedDrivingAssertionPlan $plan*/
        foreach($repeatedPlansInRange as $plan) {
            if($plan->getDriver()->isActive()) {
                $this->handleRepeatedDrivingAssertionsForWorkingMonth($plan, $workingMonth);
            }
        }
    }

    public function handleNewOrChangedAbsent(Absent $absent)
    {
        // TODO: Implement handleNewOrChangedAbsent() method.
    }

    public function handleMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan)
    {
        /** @var ShiftRepository $shiftRepository */
        $shiftRepository = $this->container->get('shift_repository');
        /** @var DrivingAssertionRepository $drivingAssertionRepository */
        $drivingAssertionRepository = $this->container->get('drivingassertion_repository');

        $driversPerShifts = $monthlyPlan->shifts;
        /** @var MonthlyPlanDriversPerShiftDTO $driversPerShift */
        foreach($driversPerShifts as $driversPerShift) {
            $newDrivers = $driversPerShift->newDrivers;
            /** @var MonthlyPlanDrivingAssertionDTO $newDriver */
            $shift = $shiftRepository->find($driversPerShift->shiftId);
            foreach($newDrivers as $newDriver) {
                /** @var Driver $driver */
                $driver = $newDriver->driver;
                if(null !== $driver) {
                    if(!$driver->hasDrivingAssertionForShift($shift)) {
                        $drivingAssertion = DrivingAssertion::registerDrivingAssertion($driver, $shift);
                        $drivingAssertionRepository->store($drivingAssertion);
                    }
                }
            }
        }
    }
}