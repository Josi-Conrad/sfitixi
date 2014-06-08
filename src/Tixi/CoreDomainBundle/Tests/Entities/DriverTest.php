<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entities;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class DriverTest
 * @package Tixi\CoreDomainBundle\Tests\Entities
 */
class DriverTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testDriverCRUD() {
        $driverCategory = $this->createDriverCategory('Zivildienst');
        $address = Address::registerAddress('Burstrasse 22c', '6333', 'Baar', 'Schweiz');
        $this->addressRepo->store($address);

        $driver = Driver::registerDriver(
            'm', 'Max', 'Mühlemann', '041 222 32 32',
            $address, 'F3234141', $driverCategory, true, 'Dr.', 'test@test.de', new \DateTime(), new \DateTime(),
            5, 'alles nur ein Test'
        );

        $this->driverRepo->store($driver);
        $this->em->flush();

        $driverFind = $this->driverRepo->find($driver->getId());
        $this->assertEquals($driverFind, $driver);

        $date = new \DateTime('2009-01-01');
        $address = Address::registerAddress('Hauptstrasse 11', '6430', 'Schwyz', 'Schweiz', 48.55446, 75.54659, 'Wohnung', 1);
        $this->addressRepo->store($address);
        $driverCategory = $this->createDriverCategory('Freiwillig');
        $driver->updateDriverData(
            'f', 'Muni', 'Meier', '041 333 32 32',
            $address, 'FEA12345', $driverCategory, false, 'Dr. med.', 'test@test.de', $date, $date,
            5, 'alles nur ein Test');
        $this->driverRepo->store($driver);
        $this->em->flush();

        /**@var $driverFind Driver */
        $driverFind = $this->driverRepo->findOneBy(array('licenceNumber' => 'FEA12345'));
        $this->assertEquals($driver, $driverFind);

        $this->driverCreateAbsent($driver);
        $this->driverSuperviseVehicle($driver);
        $this->driverRemove($driver);
    }

    private function driverCreateAbsent(Driver $driver) {
        $absent = Absent::registerAbsent('Ferien', new \DateTime('2014-11-12'), new \DateTime('2014-12-12'));
        $absent2 = Absent::registerAbsent('Ferien da', new \DateTime('2015-11-12'), new \DateTime('2015-12-12'));
        $this->absentRepo->store($absent);
        $this->absentRepo->store($absent2);
        $driver->assignAbsent($absent);
        $driver->assignAbsent($absent2);
        $this->driverRepo->store($driver);
        $this->em->flush();

        $found = false;
        $absents = $driver->getAbsents();
        foreach ($absents as $a) {
            if ($a->getId() == $absent2->getId()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
        $this->assertCount(2, $absents);
    }

    private function driverSuperviseVehicle(Driver $driver) {
        $vehicleCategory = VehicleCategory::registerVehicleCategory('Movano Maxi', 4, 2);
        $this->vehicleCategoryRepo->store($vehicleCategory);
        $vehicle = Vehicle::registerVehicle('Movano 1', 'CH123', new \DateTime('2012-11-11'), 1, $vehicleCategory);
        $this->vehicleRepo->store($vehicle);
        $driver->assignSupervisedVehicle($vehicle);
        $this->driverRepo->store($driver);
        $this->em->flush();

        /**@var Driver $driverFound */
        $driverFound = $this->driverRepo->find($driver->getId());
        $supervisedVehicles = $driverFound->getSupervisedVehicles();
        $found = true;
        foreach ($supervisedVehicles as $s) {
            if ($s->getId() == $vehicle->getId()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    private function driverRemove(Driver $driver) {
        $id = $driver->getId();
        Driver::removeDriver($driver);
        $this->em->remove($driver);
        $this->em->flush();
        $this->assertEquals(null, $this->driverRepo->find($id));
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

    public function tearDown() {
        parent::tearDown();
    }
}