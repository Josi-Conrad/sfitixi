<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 10:13
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class DocumentTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDocumentMonthPlan() {
        $date = \DateTime::createFromFormat('d.m.Y', '01.07.2024');
        $success = $this->documentManagement->sendMonthPlanToAllDrivers($date);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 