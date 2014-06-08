<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 10:13
 */

namespace Tixi\App\AppBundle\Tests;


use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

class DocumentManagementTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDocumentMonthPlan() {
        $date = \DateTime::createFromFormat('d.m.Y', '01.07.2024');
        $workMonth = $this->workingMonthRepo->findWorkingMonthByDate($date);
        if (!$workMonth) {
            $workMonth = WorkingMonth::registerWorkingMonth($date);
            $this->workingMonthRepo->store($workMonth);
            $this->em->flush();
        }
        $success = $this->documentManagement->sendMonthPlanToAllDrivers($date);
        $this->assertEquals(true, $success);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 