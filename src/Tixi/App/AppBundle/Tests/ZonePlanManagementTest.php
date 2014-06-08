<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 14:24
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZonePlan;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class ZonePlanManagementTest extends CommonBaseTest {
    public function setUp() {
        parent::setUp();
    }

    public function testZonePlanFunctions() {
        $addressAesch = $this->createTestAddressAesch();
        $zone = Zone::createUnclassifiedZone();
        $this->zoneRepo->store($zone);
        $zone = Zone::registerZone('kantonal123456', 2);
        $this->zoneRepo->store($zone);
        $zonePlan = ZonePlan::registerZonePlan('habakuck', '3231');
        $zonePlan->setZone($zone);
        $this->zonePlanRepo->store($zonePlan);
        $this->em->flush();

        $this->assertTrue($this->zoneRepo->checkIfNameAlreadyExist('kantonal123456'));

        $zoneAdd = $this->zonePlanManagement->getZoneForAddress($addressAesch);
        $this->assertNotNull($zoneAdd);

        $zoneCity = $this->zonePlanManagement->getZoneForCity('habakuck');
        $this->assertNotNull($zoneCity);
    }

    public function tearDown() {
        parent::tearDown();
    }

}