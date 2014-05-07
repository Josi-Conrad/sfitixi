<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Person;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class DrivingOrderTest
 * @package Tixi\CoreDomainBundle\Tests\Entity
 */
class DrivingOrderTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDrivingOrderCRUD() {
        $addressFrom = $this->createTestAddressBaar();
        $addressTo = $this->createTestAddressGoldau();
        $passenger = Passenger::registerPassenger('m', 'Arthuro', 'Benatone', '+418182930', $addressFrom);
        $this->passengerRepo->store($passenger);

        $date = $this->dateTimeService->convertDateTimeStringToUTCDateTime('20.05.2014 00:00');
        $time = $this->dateTimeService->convertDateTimeStringToUTCDateTime('20.05.2014 15:05');

        $route = Route::registerRoute($addressFrom, $addressTo);
        $route->setDuration(15);
        $route->setDistance(6);

        $this->routeRepo->store($route);

        $drivingOrder = DrivingOrder::registerDrivingOrder($date, $time, 2, 'möchte nicht hinten sitzen');
        $drivingOrder->assignRoute($route);
        $passenger->assignDrivingOrder($drivingOrder);
        $drivingOrder->assignPassenger($passenger);
        $this->drivingOrderRepo->store($drivingOrder);
        $this->em->flush();
        $this->assertNotNull($this->drivingOrderRepo->find($drivingOrder->getId()));

        //TimePeriod from start day of month to next start day of month
        //(amount of days = amount of days in this month)
        $start = new \DateTime('now');
        $start->modify('first day of next month');

        $monthsAgo = 3;
        $monthDate = new \DateTime('today');
        $monthDate->modify('+' . $monthsAgo . ' month');
        $monthDate->format('first day of this month');
        $workingMonth = WorkingMonth::registerWorkingMonth($monthDate);
        $workingMonth->createWorkingDaysForThisMonth();
        foreach ($workingMonth->getWorkingDays() as $wd) {
            $this->workingDayRepo->store($wd);
        }
        $this->workingMonthRepo->store($workingMonth);

        $workingDays = $workingMonth->getWorkingDays();

        /**@var $shiftTypes ShiftType[] */
        $shiftTypes = $this->shiftTypeRepo->findAllNotDeleted();

        //create workingDays shifts, assign them drivingpools, get amount of needed drivers
        /** @var $workingDay WorkingDay */
        foreach ($workingDays as $workingDay) {
            /** @var $shiftType ShiftType */
            foreach ($shiftTypes as $shiftType) {
                $shift = Shift::registerShift($workingDay, $shiftType);
                $shift->setAmountOfDrivers(18);
                $workingDay->assignShift($shift);
                $this->shiftRepo->store($shift);
            }
            $this->workingDayRepo->store($workingDay);
        }

        $this->em->flush();
        $this->assertNotNull($this->workingMonthRepo->find($workingMonth->getId()));

        $drivingAssertionPlans = $this->repeatedDrivingAssertionPlanRepo->findPlanForDate(new \DateTime());
        $this->assertNotNull($drivingAssertionPlans);

        foreach($drivingAssertionPlans as $drivingAssertionPlan){
            echo $drivingAssertionPlan->getDriver()->getNameString() . "\n";
            $assertions = $drivingAssertionPlan->getRepeatedDrivingAssertions();
            foreach($assertions as $assertion){
                $shift->getWorkingDay()->getDate();
                $assertion->matching($shift);
            }
        }
    }


    public function tearDown() {
        parent::tearDown();
    }
}