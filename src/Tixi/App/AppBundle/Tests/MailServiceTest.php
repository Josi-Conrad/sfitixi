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

class MailServiceTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testMailService() {
        $recipients[] = 'itixizug@gmail.com';
        $success = $this->mailService->mailToSeveralRecipients($recipients, 'test', 'nohtml');
        $this->assertEquals(true, $success);
    }

    public function tearDown() {
        parent::tearDown();
    }
} 