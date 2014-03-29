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

    public function testVehicleCRUD() {

        $vehicleCat = $this->createVehicleCategory('Movano 1', 5, 1);
        $vehicle = Vehicle::registerVehicle('VM', 'CH002002', new \DateTime('2012-01-01'), 2, $vehicleCat);
        $this->vehicleRepo->store($vehicle);
        $this->em->flush();

        $vehicle_find = $this->vehicleRepo->find($vehicle->getId());
        $this->assertEquals($vehicle, $vehicle_find);

        $vehicle->updateBasicData('VW Maxi 1', 'CH+212331', new \DateTime('2013-02-02'), 3, $vehicleCat);
        $this->vehicleRepo->store($vehicle);
        $this->em->flush();

        $this->vehicleCreateServicePlan($vehicle);
        $this->vehicleRemove($vehicle);
    }

    private function vehicleCreateServicePlan(Vehicle $vehicle) {
        $servicePlan = $this->createServicePlan(new \DateTime('now'), new \DateTime('now'));
        $this->servicePlanRepo->store($servicePlan);
        $vehicle->assignServicePlan($servicePlan);
        $this->vehicleRepo->store($vehicle);
        $this->em->flush();

        $found = false;
        $servicePlanFind = $this->servicePlanRepo->find($servicePlan->getId());
        foreach ($vehicle->getServicePlans() as $s) {
            if ($s->getId() == $servicePlanFind->getId()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    private function vehicleRemove(Vehicle $vehicle) {
        $id = $vehicle->getId();
        Vehicle::removeVehicle($vehicle);
        $this->vehicleRepo->remove($vehicle);
        $this->em->flush();
        $this->assertEquals(null, $this->vehicleRepo->find($id));
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
            $vehicleCat = VehicleCategory::registerVehicleCategory($name, $seats, $wheelchairs);
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