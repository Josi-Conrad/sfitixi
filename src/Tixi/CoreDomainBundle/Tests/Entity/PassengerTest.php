<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\CoreDomain\Absent;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Handicap;
use Tixi\CoreDomain\Passenger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PassengerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\PassengerRepositoryDoctrine
     */
    private $passangerRepo;

    /**
     * @var \Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine
     */
    private $handicapRepo;

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

        $this->passangerRepo = $kernel
            ->getContainer()
            ->get('passenger_repository');

        $this->handicapRepo = $kernel
            ->getContainer()
            ->get('handicap_repository');

        $this->addressRep = $kernel
            ->getContainer()
            ->get('address_repository');

        $this->absentRepo = $kernel
            ->getContainer()
            ->get('absent_repository');

        $this->em->beginTransaction();
    }

    public function test() {
        $handicap1 = $this->createHandicap('IV');
        $handicap2 = $this->createHandicap('AHV');

        $absent = Absent::registerAbsent('Ferien', '2014-12-12',  '2014-12-12');
        $this->absentRepo->store($absent);

        $address = Address::registerAddress('Hauptstrasse', '15b', 'Hühneberg', '5380', 'Schweiz');
        $this->addressRep->store($address);

        $passenger = Passenger::registerPassenger(
            'Herr', 'Max', 'Muster', '041 232 32 32', $address, $handicap1, true
        );

        $passenger->assignAbsent($absent);

        $this->passangerRepo->store($passenger);


        $this->em->flush();

        $passenger_find = $this->passangerRepo->find($passenger->getId());
        $this->assertEquals($passenger, $passenger_find);
        $this->assertEquals($passenger->getHandicap()->getName(), $passenger_find->getHandicap()->getName());
    }

    /**
     * @param $name
     * @return null|object|Handicap
     */
    private function createHandicap($name) {
        $handicap = $this->handicapRepo->findOneBy(array('name' => $name));
        if (empty($handicap)) {
            $handicap = Handicap::registerHandicap($name);
            $this->handicapRepo->store($handicap);
        }
        return $handicap;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}