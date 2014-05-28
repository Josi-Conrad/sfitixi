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

class RideTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

   public function testFeasibility() {
        $day = new \DateTime('2014-06-01 00:00:00');
        $time = new \DateTime('2014-06-01 08:15:00');
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