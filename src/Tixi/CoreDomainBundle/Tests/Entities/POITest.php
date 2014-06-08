<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entities;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIKeyword;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class POITest
 * @package Tixi\CoreDomainBundle\Tests\Entities
 */
class POITest extends CommonBaseTest{

    public function setUp() {
        parent::setUp();
    }

    public function testAddressCRUD() {
        $address = Address::registerAddress('Seeweg 22b', '6333', 'Baar',
            'Schweiz', 'Wohnadresse', 47.175460, 8.517752, 'Wohnung');
        $this->addressRepo->store($address);
        $this->em->flush();
        $addressFind = $this->addressRepo->find($address->getId());
        $this->assertEquals($address, $addressFind);

        $address->updateAddressData('Hauptstrasse 23', '6430', 'Schwyz', 'Schweiz', 'SZKB', 48.2108, 57.1228, 'Bank');
        $this->addressRepo->store($address);
        $this->em->flush();

        $addressFind = $this->addressRepo->find($address->getId());
        $this->assertEquals($address, $addressFind);

        $this->addressRemove($address);
    }

    private function addressRemove(Address $address) {
        $id = $address->getId();
        Address::removeAddress($address);
        $this->em->remove($address);
        $this->em->flush();
        $this->assertEquals(null, $this->addressRepo->find($id));
    }

    public function testPoiCRUD() {
        $address = Address::registerAddress('Grundstrasse 12', '6431', 'Schwyz',
            'Schweiz', 'Spital Schwyz', 47.175460, 8.517752, 'Spital');
        $this->addressRepo->store($address);
        $poiKeyword1 = POIKeyword::registerPOIKeyword('Tet1234');
        $poiKeyword2 = POIKeyword::registerPOIKeyword('Test2345');
        $poiKeyword3 = POIKeyword::registerPOIKeyword('Test3456');
        $this->poiKeywordRepo->store($poiKeyword1);
        $this->poiKeywordRepo->store($poiKeyword2);
        $this->poiKeywordRepo->store($poiKeyword3);
        $poi = POI::registerPoi('Krankenhaus', $address, 'Therapie', '041 818 21 21');
        $poi->assignKeyword($poiKeyword1);
        $poi->assignKeyword($poiKeyword2);
        $poi->assignKeyword($poiKeyword3);
        $this->poiRepo->store($poi);
        $this->em->flush();

        $poiFind = $this->poiRepo->find($poi->getId());
        $this->assertEquals($poi, $poiFind);

        $poi->updatePOIData('Altersheim Wohnwohl', null, 'Pflege', '041 818 31 31', 'Gutes Heim');
        $this->poiRepo->store($poi);
        $this->em->flush();

        $poiFind = $this->poiRepo->find($poi->getId());
        $this->assertEquals($poi, $poiFind);

        $this->poiRemove($poi);

    }

    private function poiRemove(POI $poi) {
        $id = $poi->getId();
        POI::removePoi($poi);
        $this->em->remove($poi);
        $this->em->flush();
        $this->assertEquals(null, $this->poiRepo->find($id));
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
        parent::tearDown();
    }
}