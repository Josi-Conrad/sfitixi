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

    public function testDocumentMonthPlan() {
        $date = \DateTime::createFromFormat('d.m.Y', '01.07.2024');
        $success = $this->documentManagement->sendMonthPlanToAllDrivers($date);
        $this->assertEquals(true, $success);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 