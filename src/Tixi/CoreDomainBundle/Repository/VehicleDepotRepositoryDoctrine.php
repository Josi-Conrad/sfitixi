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

    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name) {
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.isDeleted = 0')
            ->andWhere('s.name = :duplicateName')
            ->setParameter('duplicateName', $name);
        if ($qb->getQuery()->getResult()) {
            return true;
        } else {
            return false;
        }
    }
}