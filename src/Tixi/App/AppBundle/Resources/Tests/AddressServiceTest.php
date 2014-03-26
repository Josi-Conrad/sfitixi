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

class AddressServiceTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var AddressManagementImplDoctrine
     */
    private $aService;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->aService = $kernel->getContainer()->get('tixi_app.address_management');
    }

    public function testSearchAddress() {
        $results = $this->aService->getAddressSuggestionsByString('Seeweg Zug');
        $this->assertNotEmpty($results);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 