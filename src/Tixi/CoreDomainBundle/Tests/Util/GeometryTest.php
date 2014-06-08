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

        $i = GeometryService::serialize(47.54085);
        $this->assertEquals(475408500, $i);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 