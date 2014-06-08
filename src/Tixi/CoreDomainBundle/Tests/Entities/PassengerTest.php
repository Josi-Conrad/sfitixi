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
use Tixi\CoreDomain\PersonCategory;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class PassengerTest
 * @package Tixi\CoreDomainBundle\Tests\Entities
 */
class PassengerTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testPassengerCRUD() {
        $handicap = $this->createHandicap('hörbehindert');
        $insurance = $this->createInsurance('AHV');
        $category = $this->createPersonCategory('Sponsor');

        $address = Address::registerAddress('Teststrasse 142', '6360', 'Cham', 'Schweiz');
        $this->addressRepo->store($address);

        $passenger = Passenger::registerPassenger(
            'f', 'Toranto', 'Testinger', '041 324 33 22',
            $address, 'Herro', true, false, 'test@test.de', new \DateTime(), new \DateTime(),
            5, 'alles nur ein Test', 'und auch Notizen'
        );
        $passenger->assignHandicap($handicap);
        $passenger->assignInsurance($insurance);
        $passenger->assignPersonCategory($category);

        $this->passengerRepo->store($passenger);
        $this->em->flush();

        $passengerFind = $this->passengerRepo->find($passenger->getId());
        $this->assertEquals($passenger, $passengerFind);

        $passenger->updatePassengerData(
            'f', 'Mila', 'Tolina', '0293292323',
            $address, 'Lady', true, false, 'der@test.de', new \DateTime(), new \DateTime(),
            2, 'goodies', 'notices');

        $this->passengerRepo->store($passenger);
        $this->em->flush();

        $passengerFind = $this->passengerRepo->find($passenger->getId());
        $this->assertEquals($passenger, $passengerFind);

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

    private function createPersonCategory($name) {
        $category = PersonCategory::registerPersonCategory($name);
        $current = $this->personCategoryRepo->findOneBy(array('name' => $name));
        if (empty($current)) {
            $this->personCategoryRepo->store($category);
            return $category;
        }
        return $current;
    }

    public function tearDown() {
        parent::tearDown();
    }
}