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

/**
 * Class VehicleRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class VehicleRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements VehicleRepository{
    /**
     * @param Vehicle $vehicle
     * @return mixed|void
     */
    public function store(Vehicle $vehicle)
    {
        $this->getEntityManager()->persist($vehicle);
    }

    /**
     * @param Vehicle $vehicle
     * @return mixed|void
     */
    public function remove(Vehicle $vehicle)
    {
        $this->getEntityManager()->remove($vehicle);
    }

    public function getAmountByVehicleCategory(VehicleCategory $category)
    {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        $qb->add('where', 'e.category = :category');
        $qb->setParameter('category', $category);
        return $qb->getQuery()->getSingleScalarResult();
    }
}