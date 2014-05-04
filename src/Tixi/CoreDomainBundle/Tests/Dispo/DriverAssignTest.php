<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class DriverAssignTest
 * @package Tixi\CoreDomainBundle\Tests\Entity
 */
class DriverAssignTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDateFunction(){
        $date = new \DateTime('2014-05');
        echo $date->format('Y-m-d');
        $workingMonth = WorkingMonth::registerWorkingMonth($date);
        $this->workingMonthRepo->store($workingMonth);
        $this->em->flush();
        $workingMonth = $this->workingMonthRepo->findWorkingMonthByDate($date);
        $this->assertNotNull($workingMonth);
    }

    public function testRepeatedDrivingAssertionCRUD() {

        $driverCategory = $this->createDriverCategory('Zivildienst');

        $address = Address::registerAddress('Burstrasse 22c', '6333', 'Baar', 'Schweiz');
        $this->addressRepo->store($address);

        $driver = Driver::registerDriver(
            'm', 'Max', 'Mühlemann', '041 222 32 32',
            $address, 'F3234141', $driverCategory, true, '', 'test@test.de', new \DateTime(), new \DateTime(),
            5, 'alles nur ein Test'
        );
        $this->driverRepo->store($driver);

        $shiftType = $this->createShiftType('Shift 1');

        $repeatedDrivingAssertionWeekly = new RepeatedWeeklyDrivingAssertion();
        $repeatedDrivingAssertionWeekly->setWeekday(1);
        $this->repeatedDrivingAssertionRepo->store($repeatedDrivingAssertionWeekly);

        $repeatedDrivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            'test', new \DateTime(), 'weekly', true);
        $repeatedDrivingAssertionPlan->assignDriver($driver);
        $this->repeatedDrivingAssertionPlanRepo->store($repeatedDrivingAssertionPlan);
        $this->em->flush();

        $find = $this->repeatedDrivingAssertionPlanRepo->find($repeatedDrivingAssertionPlan->getId());
        $this->assertEquals($find, $repeatedDrivingAssertionPlan);

    }

    private function createDriverCategory($name) {
        $driverCategory = DriverCategory::registerDriverCategory($name);
        $current = $this->driverCategoryRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $this->driverCategoryRepo->store($driverCategory);
            return $driverCategory;
        }
        return $current;
    }

    private function createShiftType($name) {
        $shiftType = ShiftType::registerShiftType($name, new \DateTime(), new \DateTime());
        $current = $this->shiftTypeRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $this->shiftTypeRepo->store($shiftType);
            return $shiftType;
        }
        return $current;
    }

    public function tearDown() {
        parent::tearDown();
    }
}