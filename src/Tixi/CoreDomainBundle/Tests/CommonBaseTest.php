<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.04.14
 * Time: 11:49
 */

namespace Tixi\CoreDomainBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIKeyword;
use Tixi\CoreDomain\Address;

class CommonBaseTest extends WebTestCase{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PersonRepositoryDoctrine
     */
    protected $personRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PassengerRepositoryDoctrine
     */
    protected $passengerRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine
     */
    protected $driverRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine
     */
    protected $driverCategoryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AbsentRepositoryDoctrine
     */
    protected $absentRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine
     */
    protected $handicapRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\InsuranceRepositoryDoctrine
     */
    protected $insuranceRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine
     */
    protected $vehicleRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleCategoryRepositoryDoctrine
     */
    protected $vehicleCategoryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\ServicePlanRepositoryDoctrine
     */
    protected $servicePlanRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine
     */
    protected $addressRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIRepositoryDoctrine
     */
    protected $poiRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\POIKeywordRepositoryDoctrine
     */
    protected $poiKeywordRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingMissionRepositoryDoctrine
     */
    protected $drivingMissionRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingOrderRepositoryDoctrine
     */
    protected $drivingOrderRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingPoolRepositoryDoctrine
     */
    protected $drivingPoolRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionPlanRepositoryDoctrine
     */
    protected $repeatedDrivingAssertionPlanRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionRepositoryDoctrine
     */
    protected $repeatedDrivingAssertionRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\ShiftRepositoryDoctrine
     */
    protected $shiftRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\ShiftTypeRepositoryDoctrine
     */
    protected $shiftTypeRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RouteRepositoryDoctrine
     */
    protected $routeRepo;
    /**
     * @var \Tixi\SecurityBundle\Repository\UserRepositoryDoctrine
     */
    protected $userRepo;
    /**
     * @var \Tixi\SecurityBundle\Repository\RoleRepositoryDoctrine
     */
    protected $roleRepo;
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    protected $encFactory;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('entity_manager');

        $this->personRepo = $kernel->getContainer()->get('person_repository');
        $this->driverRepo = $kernel->getContainer()->get('driver_repository');
        $this->passengerRepo = $kernel->getContainer()->get('passenger_repository');
        $this->driverCategoryRepo = $kernel->getContainer()->get('drivercategory_repository');
        $this->absentRepo = $kernel->getContainer()->get('absent_repository');
        $this->handicapRepo = $kernel->getContainer()->get('handicap_repository');
        $this->insuranceRepo = $kernel->getContainer()->get('insurance_repository');

        $this->vehicleRepo = $kernel->getContainer()->get('vehicle_repository');
        $this->vehicleCategoryRepo = $kernel->getContainer()->get('vehiclecategory_repository');
        $this->servicePlanRepo = $kernel->getContainer()->get('serviceplan_repository');

        $this->addressRepo = $kernel->getContainer()->get('address_repository');

        $this->poiRepo = $kernel->getContainer()->get('poi_repository');
        $this->poiKeywordRepo = $kernel->getContainer()->get('poikeyword_repository');

        $this->routeRepo = $kernel->getContainer()->get('route_repository');

        $this->shiftRepo = $kernel->getContainer()->get('shift_repository');
        $this->shiftTypeRepo = $kernel->getContainer()->get('shifttype_repository');
        $this->repeatedDrivingAssertionRepo = $kernel->getContainer()->get('repeateddrivingassertion_repository');
        $this->repeatedDrivingAssertionPlanRepo = $kernel->getContainer()->get('repeateddrivingassertionplan_repository');

        $this->userRepo = $kernel->getContainer()->get('tixi_user_repository');
        $this->roleRepo = $kernel->getContainer()->get('tixi_role_repository');
        $this->encFactory = $kernel->getContainer()->get('security.encoder_factory');

        $this->em->beginTransaction();

        $this->createTestData();
    }

    public function testBaseSetup(){
        $this->assertNotEmpty($this->em);
    }

    protected function createTestData(){
    }

    public function tearDown() {
        $this->em->rollback();
        parent::tearDown();
    }
} 