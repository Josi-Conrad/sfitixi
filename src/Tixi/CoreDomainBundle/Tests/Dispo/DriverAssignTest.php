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
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverCategory;

/**
 * Class DriverAssignTest
 * @package Tixi\CoreDomainBundle\Tests\Entity
 */
class DriverAssignTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    private $addressRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine
     */
    private $driverRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine
     */
    private $driverCategoryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingMissionRepositoryDoctrine
     */
    private $drivingMissionRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingOrderRepositoryDoctrine
     */
    private $drivingOrderRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingPoolRepositoryDoctrine
     */
    private $drivingPoolRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionPlanRepositoryDoctrine
     */
    private $repeatedDrivingAssertionPlanRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionRepositoryDoctrine
     */
    private $repeatedDrivingAssertionRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\ShiftRepositoryDoctrine
     */
    private $shiftRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\ShiftTypeRepositoryDoctrine
     */
    private $shiftTypeRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('entity_manager');

        $this->addressRepo = $kernel->getContainer()->get('address_repository');
        $this->driverRepo = $kernel->getContainer()->get('driver_repository');
        $this->driverCategoryRepo = $kernel->getContainer()->get('drivercategory_repository');
        $this->shiftTypeRepo = $kernel->getContainer()->get('shifttype_repository');
        $this->repeatedDrivingAssertionRepo = $kernel->getContainer()->get('repeateddrivingassertion_repository');
        $this->repeatedDrivingAssertionPlanRepo = $kernel->getContainer()->get('repeateddrivingassertionplan_repository');

        $this->em->beginTransaction();
    }

    public function testRepeatedDrivingAssertionCRUD() {

        $driverCategory = $this->createDriverCategory('Zivildienst');
        $address = Address::registerAddress('Burstrasse 22c', '6333', 'Baar', 'Schweiz');
        $this->addressRepo->store($address);

        $driver = Driver::registerDriver(
            'Herr', 'Max', 'Mühlemann', '041 222 32 32',
            $address, 'F3234141', $driverCategory, true, 'test@test.de', new \DateTime(), new \DateTime(),
            5, 'alles nur ein Test'
        );
        $this->driverRepo->store($driver);

        $shift = new ShiftType();
        $shift->setStart(new \DateTime());
        $shift->setEnd(new \DateTime());
        $shift->setName('Schicht 1');
        $this->shiftTypeRepo->store($shift);

        $repeatedDrivingAssertionWeekly = new RepeatedWeeklyDrivingAssertion();
        $repeatedDrivingAssertionWeekly->setWeekday(1);
        $this->repeatedDrivingAssertionRepo->store($repeatedDrivingAssertionWeekly);

        $repeatedDrivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            'test', new \DateTime(), 'weekly', true);
        $repeatedDrivingAssertionPlan->assignDriver($driver);
        $this->repeatedDrivingAssertionPlanRepo->store($repeatedDrivingAssertionPlan);
        $this->em->flush();

        $find = $this->repeatedDrivingAssertionPlanRepo->find($repeatedDrivingAssertionPlan->getId());
        $this->assertEquals($find, $repeatedDrivingAssertionPlan);

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
        $this->em->rollback();
        parent::tearDown();
    }
}