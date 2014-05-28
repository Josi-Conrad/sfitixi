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

    public function testFeasibility() {
        $day = new \DateTime('2014-06-01 10:15:00');
        $time = new \DateTime('2014-06-01 12:15:00');
        $isFeasible = $this->rideManagement->checkFeasibility($day, $time, DrivingMission::SAME_START, 28, 2);
        $this->assertNotNull($isFeasible);
        $isFeasible ? $str = "\nIs feasible" : $str = "\nIs NOT feasible";
        echo $str;
    }

    public function testOptimization() {
        $day = new \DateTime('2014-06-01 00:00:00');
        $time = new \DateTime('2014-06-01 08:15:00');
        $shift = $this->dispoManagement->getResponsibleShiftForDayAndTime($day, $time);
        if($shift !== null){
            $this->rideManagement->getOptimizedPlanForShift($shift);
        }
    }

    public function testWorkingMonthDriverAssignment() {
        $monthDate = new \DateTime('2014-06-01');
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

        $this->workingMonthManagement->assignAvailableDriversToDrivingPools($workingMonth);

        echo "\nStill not associated DrivingPools: " .
            count($this->workingMonthManagement->getAllUnassignedDrivingPoolsForMonth($workingMonth)) . "\n";
    }

    public function testHashCoordinates() {
        $add = $this->createTestAddressBaar();
        $add2 = $this->createTestAddressGoldau();

//        $this->assertEquals('308eb7f2', $add->getHashFromBigIntCoordinates());
//        $this->assertEquals('72250006', $add2->getHashFromBigIntCoordinates());

        $arr = array();
        $arr[$add->getHashFromBigIntCoordinates()] = $add;
        $arr[$add2->getHashFromBigIntCoordinates()] = $add2;

        echo "\n" . $add->getHashFromBigIntCoordinates();
        echo "\n" . $add2->getHashFromBigIntCoordinates();
//        foreach(hash_algos() as $alg){
//            echo "\n" . $alg;
//        };

        $this->assertEquals($arr[$add->getHashFromBigIntCoordinates()], $add);
    }

    public
    function tearDown() {
        parent::tearDown();
    }
} 