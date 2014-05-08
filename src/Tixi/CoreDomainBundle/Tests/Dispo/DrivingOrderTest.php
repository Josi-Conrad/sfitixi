<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\App\AppBundle\Address\GeometryService;
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
use Tixi\CoreDomain\Vehicle;
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
        $start = microtime(true);
        /** @var $workingDay WorkingDay */
        foreach ($workingDays as $workingDay) {
            /** @var $shiftType ShiftType */
            foreach ($shiftTypes as $shiftType) {
                $shift = Shift::registerShift($workingDay, $shiftType);
                $shift->setAmountOfDrivers(16);
                $workingDay->assignShift($shift);
                for ($i = 1; $i <= $shift->getAmountOfDrivers(); $i++) {
                    $drivingPool = DrivingPool::registerDrivingPool($shift);
                    $shift->assignDrivingPool($drivingPool);
                    $this->drivingPoolRepo->store($drivingPool);
                }
                $this->shiftRepo->store($shift);
            }
            $this->workingDayRepo->store($workingDay);
        }
        $this->em->flush();
        $end = microtime(true);
        echo "Time store/flush all drivingPools: " . ($end - $start) . "s\n";


        $this->assertNotNull($this->workingMonthRepo->find($workingMonth->getId()));

        $drivingAssertionPlans = $this->repeatedDrivingAssertionPlanRepo->findPlanForDate(new \DateTime());
        $this->assertNotNull($drivingAssertionPlans);

        $start = microtime(true);
        $drivingPools = array();
        foreach ($workingMonth->getWorkingDays() as $wd) {
            foreach ($wd->getShifts() as $s) {
                foreach ($s->getDrivingPools() as $dp) {
                    array_push($drivingPools, $dp);
                }
            }
        }
        $end = microtime(true);
        echo "Time get all drivingPools: " . ($end - $start) . "s\n";


        foreach ($drivingAssertionPlans as $drivingAssertionPlan) {
            $assertions = $drivingAssertionPlan->getRepeatedDrivingAssertions();
            foreach ($assertions as $assertion) {
                if ($assertion->matching($shift)) {
                    echo "\nmatches\n";
                    $drivingPool = DrivingPool::registerDrivingPool($shift);
                    $this->drivingPoolRepo->store($drivingPool);
                }
            }
        }

        $vehicles = $this->vehicleRepo->findAll();
        /**@var $vehicle Vehicle */
        foreach ($vehicles as $vehicle) {
            foreach ($vehicle->getServicePlans() as $sp) {
            }
        }
    }

    public function testDateFunctions() {
        $date = new \DateTime('first monday of this month');
        if ($date->format('w') == 1) {
            echo $date->format('w') . " weekday " . $date->format('d.m');
        }
        $f = GeometryService::serialize(47.096560);
        $i = GeometryService::deserialize($f);
        echo "\n" . $f;
        echo "\n" . $i;
    }

    public function tearDown() {
        parent::tearDown();
    }
}