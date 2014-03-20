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
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIKeyword;

class POITest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIRepositoryDoctrine
     */
    private $poiRepo;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIKeywordRepositoryDoctrine
     */
    private $poiKeywordRepo;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRep;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel
            ->getContainer()
            ->get('entity_manager');

        $this->poiRepo = $kernel
            ->getContainer()
            ->get('poi_repository');

        $this->poiKeywordRepo = $kernel
            ->getContainer()
            ->get('poikeyword_repository');

        $this->addressRep = $kernel
            ->getContainer()
            ->get('address_repository');

        $this->em->beginTransaction();
    }

    public function test() {

        $poiKeyword1 = $this->createKeyword('Therapie');
        $poiKeyword2 = $this->createKeyword('Arztpraxis');
        $poiKeyword3 = $this->createKeyword('Werkstatt');
        $poiKeyword4 = $this->createKeyword('Arbeitsplatz');

        $address = Address::registerAddress('Zentralstrasse', '1', 'Zug', '5444', 'Schweiz');
        $poi = POI::registerPoi('Spital', 'Dialyse', $address);
        $poi->assignKeyword($poiKeyword1);
        $address->assignPoi($poi);

        $this->addressRep->store($address);
        $this->poiRepo->store($poi);
        $this->em->flush();

        $poi_find = $this->poiRepo->find($poi->getId());
        $this->assertEquals($poi, $poi_find);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }

    /**
     * @param $name
     * @return null|object|POIKeyword
     */
    private function createKeyword($name) {
        $poiKeyword = $this->poiKeywordRepo->findOneBy(array('name' => $name));
        if (empty($poiKeyword)) {
            $poiKeyword = POIKeyword::registerPOIKeyword($name);
            $this->poiKeywordRepo->store($poiKeyword);
        }
        return $poiKeyword;
    }
}