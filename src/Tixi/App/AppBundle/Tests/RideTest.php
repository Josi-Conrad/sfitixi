<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 10:13
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\ApiBundle\Helper\DateTimeService;
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

class RideTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testRepeatedFeasibility() {
        $dayTime = \DateTime::createFromFormat('d.m.Y H.i', '01.07.2014 23.15');
        $endTime = \DateTime::createFromFormat('d.m.Y H.i', '01.07.2025 00.00');
        echo $this->rideManagement->checkRepeatedFeasibility($dayTime, $endTime, 1, 0, 32);
    }

    public function testFeasibility() {
        $dayTime = \DateTime::createFromFormat('d.m.Y H.i', '01.07.2024 08.15');
        $isFeasible = $this->rideManagement->checkFeasibility($dayTime, DrivingMission::SAME_START, 120, 2);
        $this->assertNotNull($isFeasible);
        $isFeasible ? $str = "\nIs feasible" : $str = "\nIs NOT feasible";
        echo $str;
    }

    public function testOptimization() {
        $dayTime = \DateTime::createFromFormat('d.m.Y H.i', '01.07.2024 08.15');
        $shift = $this->dispoManagement->getResponsibleShiftForDayAndTime($dayTime);
        if ($shift !== null) {
            $this->rideManagement->getOptimizedPlanForShift($shift);
        }
    }

    public function testHashCoordinates() {
        $add = $this->createTestAddressBaar();
        $add2 = $this->createTestAddressGoldau();


        $arr = array();
        $arr[$add->getHashFromBigIntCoordinates()] = $add;
        $arr[$add2->getHashFromBigIntCoordinates()] = $add2;

        echo "\n" . $add->getHashFromBigIntCoordinates();
        echo "\n" . $add2->getHashFromBigIntCoordinates();

        $this->assertEquals($arr[$add->getHashFromBigIntCoordinates()], $add);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 