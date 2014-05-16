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
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class DispositionTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDispo() {

        $testDate = new \DateTime('2014-06-01');
        $testDateTime = new \DateTime('2014-06-01 7:15:00');
        $this->dispoManagement->checkFeasibility(DrivingOrder::registerDrivingOrder($testDate, $testDateTime));


    }

    public function tearDown() {
        parent::tearDown();
    }
} 