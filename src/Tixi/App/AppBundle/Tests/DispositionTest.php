<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 10:13
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class DispositionTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDispo() {

        $testDate = new \DateTime('2014-06-01');
        $testDateTime = new \DateTime('2014-06-01 18:00:00');
        $s = $this->createTestAddressGoldau();
        $t = $this->createTestAddressBaar();
        $r = $this->routeManagement->getRouteFromAddresses($s, $t);
        $order = DrivingOrder::registerDrivingOrder($testDate, $testDateTime);
        $order->assignRoute($r);

        $this->dispoManagement->checkFeasibility($order);


    }

    public function testWorkingMonthDriverAssignment() {
        $workingMonth = $this->workingMonthRepo->findWorkingMonthByDate(new \DateTime('2014-06-01'));
        $this->workingMonthManagement->assignAvailableDriversToDrivingPools($workingMonth);
        echo "\nStill not associated DrivingPools: " .
            count($this->workingMonthManagement->getAllUnassignedDrivingPoolsForMonth($workingMonth)) . "\n";
    }

    public function tearDown() {
        parent::tearDown();
    }
} 