<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 11:28
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;
use Tixi\CoreDomainBundle\Util\GeometryService;

class GeometryTest extends CommonBaseTest{

    public function setUp() {
        parent::setUp(false);
    }

    public function testGeo(){
        $l = GeometryService::deserialize(475408500);
        $this->assertEquals(47.54085, $l);

        $t = 325;
        $t2 = $t/60;
        echo round($t2, 0);

        echo "\nMinutes: " . DateTimeService::getMinutesOfDay(new \DateTime('2000-02-01 23:55:00'));
    }

    public function tearDown() {
        parent::tearDown();
    }
} 