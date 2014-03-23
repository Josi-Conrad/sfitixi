<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\CoreDomainBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\CoreDomain\ServicePlan;
use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;

class VehicleTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine
     */
    private $vehicleRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\VehicleCategoryRepositoryDoctrine
     */
    private $vehicleCategoryRepo;
    /**
     * @var \Tixi\CoreDomainBundle\Repository\ServicePlanRepositoryDoctrine
     */
    private $servicePlanRepo;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->vehicleRepo = $kernel->getContainer()->get('vehicle_repository');
        $this->vehicleCategoryRepo = $kernel->getContainer()->get('vehiclecategory_repository');
        $this->servicePlanRepo = $kernel->getContainer()->get('serviceplan_repository');
        $this->em->beginTransaction();
    }

    public function testCreateVehicleAndCategory() {

        $vehicleCat = $this->createVehicleCategory('Movano', 5, 1);
        $servicePlan = $this->createServicePlan(new \DateTime('now'), new \DateTime('now'));
        $vehicle = Vehicle::registerVehicle(
            'VM', 'CH002002', new \DateTime('2012-01-01'), 2, $vehicleCat
        );
        $vehicle->assignServicePlan($servicePlan);
        $this->vehicleRepo->store($vehicle);
        $this->em->flush();
        $vehicle_find = $this->vehicleRepo->find($vehicle->getId());
        $this->assertEquals($vehicle, $vehicle_find);
        $this->assertEquals($vehicle->getCategory()->getName(), $vehicle_find->getCategory()->getName());
    }


    /**
     * @param $name
     * @param $seats
     * @param $wheelchairs
     * @return null|object|VehicleCategory
     */
    private function createVehicleCategory($name, $seats, $wheelchairs) {
        $vehicleCat = $this->vehicleCategoryRepo->findOneBy(array('name' => $name));
        if (empty($vehicleCat)) {
            $vehicleCat = new VehicleCategory($name, $seats, $wheelchairs);
            $this->vehicleCategoryRepo->store($vehicleCat);
        }
        return $vehicleCat;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }

    /**
     * @param $from
     * @param $to
     * @return ServicePlan
     */
    private function createServicePlan($from, $to) {
        $servicePlan = ServicePlan::registerServicePlan($from, $to);
        $this->servicePlanRepo->store($servicePlan);
        return $servicePlan;
    }

}