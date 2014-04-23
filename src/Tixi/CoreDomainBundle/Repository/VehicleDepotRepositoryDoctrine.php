<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 12:33
 */

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\VehicleDepot;
use Tixi\CoreDomain\VehicleDepotRepository;

/**
 * Class VehicleDepotRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class VehicleDepotRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleDepotRepository{
    /**
     * @param VehicleDepot $vehicleDepot
     * @return mixed|void
     */
    public function store(VehicleDepot $vehicleDepot)
    {
        $this->getEntityManager()->persist($vehicleDepot);
    }

    /**
     * @param VehicleDepot $vehicleDepot
     * @return mixed|void
     */
    public function remove(VehicleDepot $vehicleDepot)
    {
        $this->getEntityManager()->remove($vehicleDepot);
    }
}