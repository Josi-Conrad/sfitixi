<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\Insurance;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\Contradict;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;

/**
 * Class PersonTest
 * @package Tixi\CoreDomainBundle\Tests\Entity
 */
class PersonTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PersonRepositoryDoctrine
     */
    private $personRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine
     */
    private $driverRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine
     */
    private $driverCategoryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PassengerRepositoryDoctrine
     */
    private $passengerRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AbsentRepositoryDoctrine
     */
    private $absentRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine
     */
    private $handicapRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\InsuranceRepositoryDoctrine
     */
    private $insuranceRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine
     */
    private $vehicleRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleCategoryRepositoryDoctrine
     */
    private $vehicleCategoryRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->addressRepo = $kernel->getContainer()->get('address_repository');
        $this->personRepo = $kernel->getContainer()->get('person_repository');
        $this->driverRepo = $kernel->getContainer()->get('driver_repository');
        $this->driverCategoryRepo = $kernel->getContainer()->get('drivercategory_repository');
        $this->absentRepo = $kernel->getContainer()->get('absent_repository');
        $this->passengerRepo = $kernel->getContainer()->get('passenger_repository');
        $this->handicapRepo = $kernel->getContainer()->get('handicap_repository');
        $this->insuranceRepo = $kernel->getContainer()->get('insurance_repository');
        $this->vehicleRepo = $kernel->getContainer()->get('vehicle_repository');
        $this->vehicleCategoryRepo = $kernel->getContainer()->get('vehiclecategory_repository');
        $this->em->beginTransaction();
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
        $driver->assignBillingAddress($address);
        $driver->assignCorrespondenceAddress($address);
        $this->driverRepo->store($driver);
        $this->em->flush();

        /**
         * @var $driverFind Driver
         */
        $driverFind = $this->driverRepo->findOneBy(array('licenceNumber' => 'FEA12345'));
        $this->assertEquals($driver, $driverFind);
        $this->assertEquals($driverFind->getAddress(), $driverFind->getBillingAddress());

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

    public function testPassengerCRUD() {
        $handicap = $this->createHandicap('hörbehindert');
        $insurance = $this->createInsurance('AHV');

        $address = Address::registerAddress('Teststrasse 142', '6360', 'Cham', 'Schweiz');
        $this->addressRepo->store($address);

        $passenger = Passenger::registerPassenger(
            'f', 'Toranto', 'Testinger', '041 324 33 22',
            $address, true, true, false, '', 'test@test.de', new \DateTime(), new \DateTime(),
            5, 'alles nur ein Test', 'und auch Notizen'
        );
        $passenger->assignHandicap($handicap);
        $passenger->assignInsurance($insurance);
        $this->passengerRepo->store($passenger);
        $this->em->flush();

        $passengerFind = $this->passengerRepo->find($passenger->getId());
        $this->assertEquals($passenger, $passengerFind);

        $passenger->updatePassengerData(
            'f', 'Mila', 'Tolina', '0293292323',
            $address, true, true, false, '', 'der@test.de', new \DateTime(), new \DateTime(),
            2, 'goodies', 'notices');
        $passenger->assignBillingAddress($address);
        $passenger->assignCorrespondenceAddress($address);

        $this->passengerRepo->store($passenger);
        $this->em->flush();

        $passengerFind = $this->passengerRepo->find($passenger->getId());
        $this->assertEquals($passenger, $passengerFind);
        $this->assertEquals($passengerFind->getCorrespondenceAddress(), $passengerFind->getAddress());

        $this->passengerCreateAbsent($passenger);
        $this->passengerRemove($passenger);
    }

    private function passengerCreateAbsent(Passenger $passenger) {
        $absent = Absent::registerAbsent('Ferien', new \DateTime('2014-11-12'), new \DateTime('2014-12-12'));
        $this->absentRepo->store($absent);
        $passenger->assignAbsent($absent);
        $this->passengerRepo->store($passenger);
        $this->em->flush();

        $found = false;
        $absents = $passenger->getAbsents();
        foreach ($absents as $a) {
            if ($a->getId() == $absent->getId()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    private function passengerRemove(Passenger $passenger) {
        $id = $passenger->getId();
        Passenger::removePassenger($passenger);
        $this->em->remove($passenger);
        $this->em->flush();
        $this->assertEquals(null, $this->passengerRepo->find($id));
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

    private function createHandicap($name) {
        $handicap = Handicap::registerHandicap($name);
        $current = $this->handicapRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $this->handicapRepo->store($handicap);
            return $handicap;
        }
        return $current;
    }

    private function createInsurance($name) {
        $insurance = Insurance::registerInsurance($name);
        $current = $this->insuranceRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $this->insuranceRepo->store($insurance);
            return $insurance;
        }
        return $current;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $this->em->rollback();
        parent::tearDown();
    }
}