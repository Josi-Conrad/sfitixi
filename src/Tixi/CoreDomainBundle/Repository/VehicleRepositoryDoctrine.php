<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 12:33
 */

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\Vehicle;
use Tixi\CoreDomain\VehicleCategory;
use Tixi\CoreDomain\VehicleRepository;

class VehicleRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleRepository{

    public function store(Vehicle $vehicle)
    {
        $this->getEntityManager()->persist($vehicle);
    }

    public function remove(Vehicle $vehicle)
    {
        $this->getEntityManager()->remove($vehicle);
    }

        public function mockData()
        {
            $category = new VehicleCategory('mockCategory', 12);
            $this->getEntityManager()->persist($category);
            for($i = 1; $i <=2000; $i++) {
                $vehicle = Vehicle::registerVehicle('mockName'.$i,'mockLicence'.$i,new \DateTime(), 12, $category);
                $this->getEntityManager()->persist($vehicle);
                $this->getEntityManager()->flush();
            }

    }

}