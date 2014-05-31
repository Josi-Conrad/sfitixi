<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 14:24
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\Dispo\ZonePlan;
use Tixi\App\AppBundle\ZonePlan\Point;
use Tixi\App\AppBundle\ZonePlan\PolygonCalc;
use Tixi\CoreDomain\Zone;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class ZonePlanTest extends CommonBaseTest {
    public function setUp() {
        parent::setUp();
    }

    public function testZonePlanFunctions() {
        $addressAesch = $this->createTestAddressAesch();
        $zone = $this->zonePlanManagement->getZoneForAddress($addressAesch);
        $zone = $this->zonePlanManagement->getZoneForAddressData('habakuck', '4155');
        $res = $this->zoneRepo->checkIfNameAlreadyExist('kantonal');
    }

    public function tearDown() {
        parent::tearDown();
    }

}