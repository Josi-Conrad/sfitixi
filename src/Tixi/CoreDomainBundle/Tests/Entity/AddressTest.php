<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Handicap;

class AddressTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRepo;
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
    private $passangerRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AbsentRepositoryDoctrine
     */
    private $absentRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine
     */
    private $handicapRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIRepositoryDoctrine
     */
    private $poiRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIKeywordRepositoryDoctrine
     */
    private $poiKeywordRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->addressRepo = $kernel->getContainer()->get('address_repository');
        $this->driverRepo = $kernel->getContainer()->get('driver_repository');
        $this->driverCategoryRepo = $kernel->getContainer()->get('drivercategory_repository');
        $this->absentRepo = $kernel->getContainer()->get('absent_repository');
        $this->passangerRepo = $kernel->getContainer()->get('passenger_repository');
        $this->handicapRepo = $kernel->getContainer()->get('handicap_repository');
        $this->poiRepo = $kernel->getContainer()->get('poi_repository');
        $this->poiKeywordRepo = $kernel->getContainer()->get('poikeyword_repository');

        $this->em->beginTransaction();
    }

    public function testCreateAddress() {
        $address = Address::registerAddress(
            'Seeweg 22b',
            '6333',
            'Baar',
            'Schweiz',
            'Wohnadresse', 47.175460, 8.517752, 'Wohnung'
        );
        $this->addressRepo->store($address);
        $this->em->flush();
        $addressFind = $this->addressRepo->find($address->getId());
        $this->assertEquals($address, $addressFind);
    }

    public function testCreateDriver(){
        $driverCategory = $this->createDriverCategory('Zivildienst');
        $address = Address::registerAddress(
            'Burstrasse 22c',
            '6333',
            'Baar',
            'Schweiz'
        );
        $this->addressRepo->store($address);

        $driver = Driver::registerDriver(
            'Herr', 'Max', 'Mühlemann', '041 222 32 32', '3234141',
            $address, $driverCategory
        );
        $this->driverRepo->store($driver);

        $this->em->flush();

        $driverFind = $this->driverRepo->find($driver->getId());
        $this->assertEquals($driverFind, $driver);
    }

    public function testCreatePassenger(){
        $handicap = $this->createHandicap('IV');
        $address = Address::registerAddress(
            'Teststrasse 142',
            '6360',
            'Cham',
            'Schweiz'
        );
        $this->addressRepo->store($address);

        $passenger = Passenger::registerPassenger(
            'Frau', 'Toranto', 'Testinger', '041 324 33 22',
            $address, $handicap, true
        );
        $this->passangerRepo->store($passenger);

        $this->em->flush();

        $driverFind = $this->passangerRepo->find($passenger->getId());
        $this->assertEquals($driverFind, $passenger);
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

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}