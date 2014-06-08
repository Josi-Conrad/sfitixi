<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entities\Management;

use Tixi\CoreDomain\BankHoliday;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class BankHolidayTest
 * @package Tixi\CoreDomainBundle\Tests\Entities\Management
 */
class BankHolidayTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testBankHolidayCRUD() {
        $day = \DateTime::createFromFormat('d.m.Y', '11.11.2021');
        $bankHoliday = BankHoliday::registerBankHoliday('Feiertag', $day);
        $this->bankHolidayRepo->store($bankHoliday);
        $this->em->flush();
        $find = $this->bankHolidayRepo->find($bankHoliday->getId());
        $this->assertEquals($bankHoliday, $find);
    }


    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();
    }

}