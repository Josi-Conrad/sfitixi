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
use Tixi\CoreDomain\City;
use Tixi\CoreDomain\Country;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;
use Tixi\CoreDomain\PostalCode;

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
     * @var \Tixi\CoreDomainBundle\Repository\CityRepositoryDoctrine
     */
    private $cityRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\CountryRepositoryDoctrine
     */
    private $countryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PostalCodeRepositoryDoctrine
     */
    private $postalCodeRepo;
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
        $this->cityRepo = $kernel->getContainer()->get('city_repository');
        $this->countryRepo = $kernel->getContainer()->get('country_repository');
        $this->postalCodeRepo = $kernel->getContainer()->get('postal_code_repository');
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
        $postalCode = $this->createPostalCode('6310');
        $city = $this->createCity('Zug');
        $country = $this->createCountry('Schweiz');
        $address = Address::registerAddress(
            'Seeweg 22b', $postalCode, $city, $country,
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
            $this->createPostalCode('6333'),
            $this->createCity('Baar'),
            $this->createCountry('Schweiz')
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
    /**
     * @param $name
     * @return null|object|Country
     */
    private function createCountry($name) {
        $country = Country::registerCountry($name);
        $current = $this->countryRepo->findOneBy(array('name' => $country->getName()));
        if (empty($current)) {
            $this->countryRepo->store($country);
            return $country;
        }
        return $current;
    }

    /**
     * @param $name
     * @internal param $code
     * @return PostalCode
     */
    private function createCity($name) {
        $city = City::registerCity($name);
        $current = $this->cityRepo->findOneBy(array('name' => $city->getName()));
        if (empty($current)) {
            $this->cityRepo->store($city);
            return $city;
        }
        return $current;
    }

    /**
     * @param $code
     * @return PostalCode
     */
    private function createPostalCode($code) {
        $postalCode = PostalCode::registerPostalCode($code);
        $current = $this->postalCodeRepo->findOneBy(array('code' => $postalCode->getCode()));
        if (empty($current)) {
            $this->postalCodeRepo->store($postalCode);
            return $postalCode;
        }
        return $current;
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

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}