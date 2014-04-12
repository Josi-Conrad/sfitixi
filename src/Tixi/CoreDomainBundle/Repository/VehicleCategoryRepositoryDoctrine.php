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

/**
 * Class VehicleCategoryRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class VehicleCategoryRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleCategoryRepository{
    /**
     * @param VehicleCategory $vehicleCategory
     * @return mixed|void
     */
    public function store(VehicleCategory $vehicleCategory)
    {
        $this->getEntityManager()->persist($vehicleCategory);
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return mixed|void
     */
    public function remove(VehicleCategory $vehicleCategory)
    {
        $this->getEntityManager()->remove($vehicleCategory);
    }
}