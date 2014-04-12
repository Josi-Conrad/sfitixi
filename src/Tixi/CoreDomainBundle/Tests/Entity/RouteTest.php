<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;

/**
 * Class RouteTest
 * @package Tixi\CoreDomainBundle\Tests\Entity
 */
class RouteTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RouteRepositoryDoctrine
     */
    private $routeRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->addressRepo = $kernel->getContainer()->get('address_repository');
        $this->routeRepo = $kernel->getContainer()->get('route_repository');
        $this->em->beginTransaction();
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     */
    public function testRouteCRUD() {
        $address = Address::registerAddress('Hauptweg 11', '6333', 'Baar',
            'Schweiz', 'Wohnadresse', 47.175460, 8.517752, 'Wohnung');
        $address2 = Address::registerAddress('Hauptweg 22', '6333', 'Baar',
            'Schweiz', 'Wohnadresse', 47.135460, 8.527752, 'Wohnung');
        $this->addressRepo->store($address);
        $this->addressRepo->store($address2);
        $this->em->flush();

        $route = Route::registerRoute($address, $address2, 8, 1200);

        $finds = $this->routeRepo->findBy(array('startAddress' => $address->getId(), 'targetAddress' => $address2->getId()));
        $this->assertCount(0, $finds);

        $this->routeRepo->store($route);
        $this->em->flush();

        $finds = $this->routeRepo->findBy(array('startAddress' => $address->getId(), 'targetAddress' => $address2->getId()));
        $this->assertCount(1, $finds);

        //duplicate entry Exception will appear
        $route2 = Route::registerRoute($address, $address2, 2, 312);
        $this->routeRepo->store($route2);
        $this->em->flush();

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $this->em->rollback();
        parent::tearDown();
    }
}