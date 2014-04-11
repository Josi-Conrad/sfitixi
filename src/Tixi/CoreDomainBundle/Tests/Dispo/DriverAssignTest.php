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
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
use Tixi\CoreDomain\Driver;

class DriverAssignTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine
     */
    private $driverRepo;
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

        $this->driverRepo = $kernel->getContainer()->get('driver_repository');
        $this->shiftTypeRepo = $kernel->getContainer()->get('shifttype_repository');
        $this->repeatedDrivingAssertionRepo = $kernel->getContainer()->get('repeateddrivingassertion_repository');
        $this->repeatedDrivingAssertionPlanRepo = $kernel->getContainer()->get('repeateddrivingassertionplan_repository');

        $this->em->beginTransaction();
    }

    public function testRepeatedDrivingAssertionCRUD() {

        /** @var Driver $driver */
        $driver = $this->driverRepo->find(1);

        $repeatedDrivingAssertionWeekly = new RepeatedWeeklyDrivingAssertion();
        $repeatedDrivingAssertionWeekly->setWeekday(1);
        $repeatedDrivingAssertionWeekly->addShiftType($this->shiftTypeRepo->find(1));
        $repeatedDrivingAssertionWeekly->addShiftType($this->shiftTypeRepo->find(2));
        $this->repeatedDrivingAssertionRepo->store($repeatedDrivingAssertionWeekly);

        $repeatedDrivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
            'test', new \DateTime(), 'weekly', true);
        $repeatedDrivingAssertionPlan->assignDriver($driver);
        $this->repeatedDrivingAssertionPlanRepo->store($repeatedDrivingAssertionPlan);
        $this->em->flush();

        $repeatedPlans = $driver->getRepeatedDrivingAssertionPlans();
        $found = false;
        foreach ($repeatedPlans as $rp) {
            if ($rp->getId() == $repeatedDrivingAssertionPlan->getId()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
        $find = $this->repeatedDrivingAssertionPlanRepo->find($repeatedDrivingAssertionPlan->getId());
        $this->assertEquals($find, $repeatedDrivingAssertionPlan);

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}