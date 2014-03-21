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
use Tixi\CoreDomain\PostalCode;

class CityTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\CityRepositoryDoctrine
     */
    private $cityRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel
            ->getContainer()
            ->get('entity_manager');

        $this->cityRepo = $kernel
            ->getContainer()
            ->get('address_city_repository');

        //$this->em->beginTransaction();
    }

    public function test() {

        $city = $this->cityRepo->storeAndGetCity(City::registerCity('Test'));
        $this->em->flush();

        $city = $this->cityRepo->storeAndGetCity(City::registerCity('Test'));
        $this->em->flush();

        $city = $this->cityRepo->storeAndGetCity(City::registerCity('Test'));
        $this->em->flush();

        $address_find = $this->cityRepo->find($city->getId());
        $this->assertEquals($city, $address_find);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        //$this->em->rollback();
    }
}