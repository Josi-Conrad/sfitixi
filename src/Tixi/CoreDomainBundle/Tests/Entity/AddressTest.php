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

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel
            ->getContainer()
            ->get('entity_manager');
        $this->addressRepo = $kernel
            ->getContainer()
            ->get('address_repository');
        $this->cityRepo = $kernel
            ->getContainer()
            ->get('city_repository');
        $this->countryRepo = $kernel
            ->getContainer()
            ->get('country_repository');
        $this->postalCodeRepo = $kernel
            ->getContainer()
            ->get('postal_code_repository');

        //$this->em->beginTransaction();
    }

    public function test() {

        $address = Address::registerAddress('Heimadresse', 'Seeweg 22b',
            $this->postalCodeRepo->storeAndGetPostalCode(PostalCode::registerPostalCode('6310')),
            $this->cityRepo->storeAndGetCity(City::registerCity('Zug')),
            $this->countryRepo->storeAndGetCountry(Country::registerCountry('Schweiz')),
            47.175460,
            8.517752,
            'Wohnung'
        );

        $this->addressRepo->store($address);

        $this->em->flush();

        $address_find = $this->addressRepo->find($address->getId());
        $this->assertEquals($address, $address_find);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        //$this->em->rollback();
    }
}