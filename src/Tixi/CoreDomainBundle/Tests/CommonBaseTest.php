<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.04.14
 * Time: 11:49
 */

namespace Tixi\CoreDomainBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\App\Address\AddressManagement;
use Tixi\App\AppBundle\Routing\RoutingMachineOSRM;
use Tixi\App\Disposition\DispositionManagement;
use Tixi\App\Disposition\WorkingMonthManagement;
use Tixi\App\Ride\RideManagement;
use Tixi\App\Routing\RouteManagement;
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIKeyword;
use Tixi\CoreDomain\Address;

class CommonBaseTest extends WebTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PersonRepositoryDoctrine
     */
    protected $personRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\PersonCategoryRepositoryDoctrine
     */
    protected $personCategoryRepo;
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
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingOrderRepositoryDoctrine
     */
    protected $repeatedDrivingOrderRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingOrderPlanRepositoryDoctrine
     */
    protected $repeatedDrivingOrderPlanRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\DrivingPoolRepositoryDoctrine
     */
    protected $drivingPoolRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\WorkingDayRepositoryDoctrine
     */
    protected $workingDayRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\Dispo\WorkingMonthRepositoryDoctrine
     */
    protected $workingMonthRepo;
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
     * @var \Tixi\CoreDomainBundle\Repository\ZoneRepositoryDoctrine
     */
    protected $zoneRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\ZonePlanRepositoryDoctrine
     */
    protected $zonePlanRepo;
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
    /**
     * @var \Tixi\App\AppBundle\ZonePlan\ZonePlanManagementImpl
     */
    protected $zonePlanManagement;
    /**
     * @var \Tixi\ApiBundle\Helper\DateTimeService
     */
    protected $dateTimeService;
    /**
     * @var RoutingMachineOSRM
     */
    protected $routingMachine;
    /**
     * @var RouteManagement
     */
    protected $routeManagement;
    /**
     * @var AddressManagement
     */
    protected $addressManagement;
    /**
     * @var DispositionManagement
     */
    protected $dispoManagement;
    /**
     * @var RideManagement
     */
    protected $rideManagement;
    /**
     * @var WorkingMonthManagement
     */
    protected $workingMonthManagement;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        set_time_limit(600);

        $this->em = $kernel->getContainer()->get('entity_manager');

        $this->personRepo = $kernel->getContainer()->get('person_repository');
        $this->personCategoryRepo = $kernel->getContainer()->get('personcategory_repository');
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
        $this->zoneRepo = $kernel->getContainer()->get('zone_repository');
        $this->zonePlanRepo = $kernel->getContainer()->get('zoneplan_repository');

        $this->drivingOrderRepo = $kernel->getContainer()->get('drivingorder_repository');
        $this->repeatedDrivingOrderRepo = $kernel->getContainer()->get('repeateddrivingorder_repository');
        $this->repeatedDrivingOrderPlanRepo = $kernel->getContainer()->get('repeateddrivingorderplan_repository');
        $this->drivingMissionRepo = $kernel->getContainer()->get('drivingmission_repository');
        $this->drivingPoolRepo = $kernel->getContainer()->get('drivingpool_repository');
        $this->workingDayRepo = $kernel->getContainer()->get('workingday_repository');
        $this->workingMonthRepo = $kernel->getContainer()->get('workingmonth_repository');

        $this->userRepo = $kernel->getContainer()->get('tixi_user_repository');
        $this->roleRepo = $kernel->getContainer()->get('tixi_role_repository');
        $this->encFactory = $kernel->getContainer()->get('security.encoder_factory');

        $this->zonePlanManagement = $kernel->getContainer()->get('tixi_app.zoneplanmanagement');
        $this->dateTimeService = $kernel->getContainer()->get('tixi_api.datetimeservice');

        $this->routingMachine = $kernel->getContainer()->get('tixi_app.routingmachine');
        $this->routeManagement = $kernel->getContainer()->get('tixi_app.routemanagement');
        $this->addressManagement = $kernel->getContainer()->get('tixi_app.addressmanagement');
        $this->dispoManagement = $kernel->getContainer()->get('tixi_app.dispomanagement');
        $this->rideManagement = $kernel->getContainer()->get('tixi_app.ridemanagement');
        $this->workingMonthManagement = $kernel->getContainer()->get('tixi_app.workingmonthmanagement');

        $this->em->beginTransaction();
    }

    public function testBase() {
        $this->assertNotNull($this->em);
    }

    protected function createTestAddressBaar() {
        $address = Address::registerAddress('Rathausstrasse 1', '6340',
            'Baar', 'Schweiz', 'Ganztagesschule mit Montessoriprofil', 47.194715, 8.526096);
        $this->addressRepo->store($address);
        $this->em->flush();
        return $address;
    }

    protected function createTestAddressArth() {
        $address = Address::registerAddress('Rigiweg 2A', '6415',
            'Arth', 'Schweiz', 'Rigi Gliders', 47.062447, 8.522211);
        $this->addressRepo->store($address);
        $this->em->flush();
        return $address;
    }

    protected function createTestAddressGoldau() {
        $address = Address::registerAddress('Bahnhofstrasse 9', '6410',
            'Arth', 'Schweiz', 'CSS', 47.049536, 8.547931);
        $this->addressRepo->store($address);
        $this->em->flush();
        return $address;
    }

    protected function createTestAddressAesch() {
        $address = Address::registerAddress('Test 9', '4147',
            'Aesch', 'Schweiz', 'Test', 47.049536, 8.547931);
        $this->addressRepo->store($address);
        $this->em->flush();
        return $address;
    }

    public function tearDown() {
        $this->em->rollback();
        parent::tearDown();
    }
} 