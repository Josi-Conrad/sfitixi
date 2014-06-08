<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entities\Management;

use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class ShiftTest
 * @package Tixi\CoreDomainBundle\Tests\Entities\Management
 */
class ShiftTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testShiftCRUD() {
        $from = \DateTime::createFromFormat('H:i', '10:00');
        $to = \DateTime::createFromFormat('H:i', '14:00');
        $shiftType = ShiftType::registerShiftType('Test123', $from, $to);
        $this->shiftTypeRepo->store($shiftType);
        $this->em->flush();
        $find = $this->shiftTypeRepo->find($shiftType->getId());
        $this->assertEquals($shiftType, $find);

        $workDay = WorkingDay::registerWorkingDay(new \DateTime());
        $this->workingDayRepo->store($workDay);

        $shift = Shift::registerShift($workDay, $shiftType, 12);
        $this->shiftRepo->store($shift);
        $this->em->flush();
        $find = $this->shiftRepo->find($shift->getId());
        $this->assertEquals($shift, $find);
    }


    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();
    }

}