<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 10:13
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\App\AppBundle\Ride\RideNode;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class DispositionTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testWorkingMonthDriverAssignment() {
        $monthDate = new \DateTime('2014-07-01');
        $workingMonth = $this->workingMonthRepo->findWorkingMonthByDate($monthDate);

        if ($workingMonth === null) {
            $workingMonth = WorkingMonth::registerWorkingMonth($monthDate);
            $workingMonth->setMemo('Test');
            $workingMonth->createWorkingDaysForThisMonth();

            $this->workingMonthRepo->store($workingMonth);
            $workingDays = $workingMonth->getWorkingDays();

            $shiftTypes = $this->shiftTypeRepo->findAllActive();

            //create workingDays shifts, assign them drivingpools, get amount of needed drivers
            /** @var $workingDay WorkingDay */
            foreach ($workingDays as $workingDay) {
                /** @var $shiftType ShiftType */
                foreach ($shiftTypes as $shiftType) {
                    $shift = Shift::registerShift($workingDay, $shiftType);
                    $shift->setAmountOfDrivers(rand(12, 20));
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
        }

        //$this->workingMonthManagement->assignAvailableDriversToDrivingPools($workingMonth);

        echo "\nStill not associated DrivingPools: " .
            count($this->workingMonthManagement->getAllUnassignedDrivingPoolsForMonth($workingMonth)) . "\n";
    }

    public function tearDown() {
        parent::tearDown();
    }
} 