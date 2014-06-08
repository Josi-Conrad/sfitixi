<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entities\Management;

use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZonePlan;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class ZonePlanManagementTest
 * @package Tixi\CoreDomainBundle\Tests\Entities\Management
 */
class ZonePlanTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testZonePlanCRUD() {
        $zone = $this->createZone('Kantonal', 1);
        $city = 'Baar';
        $zonePlan = ZonePlan::registerZonePlan($city, '4664', 'memo');
        if (!$this->zonePlanRepo->checkIfCityAlreadyExist($city)) {
            $this->zonePlanRepo->store($zonePlan);
            $zone->assignZonePlan($zonePlan);
        } else {
            $zonePlan = $this->zonePlanRepo->findOneBy(array('city' => $city));
            $zone->assignZonePlan($zonePlan);
        }
        $this->em->flush();

        $zonePlanFind = $this->zonePlanRepo->find($zonePlan->getId());
        $this->assertEquals($zonePlan, $zonePlanFind);

        $city2 = 'Baar1234567890123789123';
        $zonePlan->updateZonePlan($city2, '2372');
        $this->em->flush();

        $find = $this->zonePlanRepo->findOneBy(array('city' => $city2));
        $this->assertEquals($zonePlan, $find);
    }

    private function createZone($name, $priority) {
        $current = $this->zoneRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $zone = Zone::registerZone($name, $priority);
            $this->zoneRepo->store($zone);
            return $zone;
        }
        return $current;
    }


    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();
    }

}