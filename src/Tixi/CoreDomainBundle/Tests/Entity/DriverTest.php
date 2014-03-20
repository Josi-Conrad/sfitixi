<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\DriverCategory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DriverTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine
     */
    private $driverRepo;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine
     */
    private $driverCategoryRepo;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRep;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\AbsentRepositoryDoctrine
     */
    private $absentRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel
            ->getContainer()
            ->get('entity_manager');

        $this->driverRepo = $kernel
            ->getContainer()
            ->get('driver_repository');

        $this->driverCategoryRepo = $kernel
            ->getContainer()
            ->get('drivercategory_repository');

        $this->addressRep = $kernel
            ->getContainer()
            ->get('address_repository');

        $this->absentRepo = $kernel
            ->getContainer()
            ->get('absent_repository');

        $this->em->beginTransaction();
    }

    public function test() {

        $driverCat1 = $this->createDriverCategory('Zivildienst');
        $driverCat2 = $this->createDriverCategory('Freiwillig');
        $driverCat3 = $this->createDriverCategory('Mitglied');

        $absent = Absent::registerAbsent('Ferien', '2014-11-01', '2014-12-12');
        $this->absentRepo->store($absent);

        $address = Address::registerAddress('Seeweg', '22', 'Cham', '5433', 'Schweiz');
        $this->addressRep->store($address);

        $driver = $this->createDriver('Herr', 'Max', 'Mustermann', '031 239 22 32', '30291301923',
            $address, $driverCat2, $absent);

        $this->em->flush();

        $driver_find = $this->driverRepo->find($driver->getId());
        $this->assertEquals($driver, $driver_find);
        $this->assertEquals($driver->getDriverCategory()->getName(), $driver_find->getDriverCategory()->getName());
    }

    /**
     * @param $name
     * @return null|object|DriverCategory
     */
    public function createDriverCategory($name) {
        $driverCat = $this->driverCategoryRepo->findOneBy(array('name' => $name));
        if (empty($driverCat)) {
            $driverCat = DriverCategory::registerDriverCategory($name);
            $this->driverCategoryRepo->store($driverCat);
        }
        return $driverCat;
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $licenseNumber
     * @param $address
     * @param $driverCat
     * @param $absent
     * @return Driver
     */
    public function createDriver($title, $firstname, $lastname, $telephone, $licenseNumber,
                                 $address, $driverCat, $absent = null) {
        $driver = Driver::registerDriver(
            $title, $firstname, $lastname, $telephone, $licenseNumber, $address, $driverCat
        );
        if (!empty($absent)) {
            $driver->assignAbsent($absent);
        }
        $this->driverRepo->store($driver);
        return $driver;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}