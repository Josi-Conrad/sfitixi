<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.03.14
 * Time: 11:31
 */

namespace Tixi\App\AppBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\App\AppBundle\Controller\AddressManagementImplDoctrine;
use Tixi\CoreDomain\Address;

/**
 * Class AddressServiceTest
 * @package Tixi\App\AppBundle\Tests
 */
class AddressServiceTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var AddressManagementImplDoctrine
     */
    private $aService;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRepo;

    /**
     * Fulltext index only creates on a committed flush on database.
     * For testing purposes we can't use transactions with rollbacks.
     */
    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->aService = $kernel->getContainer()->get('tixi_app.addressmanagement');
        $this->addressRepo = $kernel->getContainer()->get('address_repository');
    }

    public function testSearchAddress() {
//        $address = Address::registerAddress('Jasldkjasdijsd 12', '6331', 'Zug', 'Schweiz');
//        $this->addressRepo->store($address);
//        $address = Address::registerAddress('Jasldkjasdijsd 12', '6330', 'Baar', 'Schweiz');
//        $this->addressRepo->store($address);
//        $this->em->flush();
//
//        $results = $this->aService->getAddressSuggestionsByString('Jasldkjasd Zug');
//        $this->assertNotCount(0, $results);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 