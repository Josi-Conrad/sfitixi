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
use Tixi\CoreDomain\VehicleCategoryRepository;
use Tixi\CoreDomain\VehicleRepository;

class VehicleCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleCategoryRepository{

    public function store(VehicleCategory $vehicleCategory)
    {
        $this->getEntityManager()->persist($vehicleCategory);
    }

    public function remove(VehicleCategory $vehicleCategory)
    {
        $this->getEntityManager()->remove($vehicleCategory);
    }
}