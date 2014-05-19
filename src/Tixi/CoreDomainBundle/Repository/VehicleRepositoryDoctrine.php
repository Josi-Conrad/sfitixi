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
use Tixi\CoreDomain\VehicleDepot;
use Tixi\CoreDomain\VehicleRepository;

/**
 * Class VehicleRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class VehicleRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleRepository {
    /**
     * @param Vehicle $vehicle
     * @return mixed|void
     */
    public function store(Vehicle $vehicle) {
        $this->getEntityManager()->persist($vehicle);
    }

    /**
     * @param Vehicle $vehicle
     * @return mixed|void
     */
    public function remove(Vehicle $vehicle) {
        $this->getEntityManager()->remove($vehicle);
    }

    /**
     * @param VehicleCategory $category
     * @return mixed
     */
    public function getAmountByVehicleCategory(VehicleCategory $category) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.category = :category');
        $qb->setParameter('category', $category);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param VehicleDepot $depot
     * @return mixed
     */
    public function getAmountByVehicleDepot(VehicleDepot $depot) {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.depot = :depot');
        $qb->setParameter('depot', $depot);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Vehicle[]
     */
    public function findAllNotDeleted(){
        $qb = parent::createQueryBuilder('s');
        $qb->select()
            ->where('s.isDeleted = 0');
        return $qb->getQuery()->getResult();
    }
}