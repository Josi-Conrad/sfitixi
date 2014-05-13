<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:40
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Tixi\CoreDomain\Dispo\RepeatedDrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RepeatedDrivingOrderRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingOrderRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingOrderRepository {

    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     * @return mixed|void
     */
    public function store(RepeatedDrivingOrder $repeatedDrivingOrder) {
        $this->getEntityManager()->persist($repeatedDrivingOrder);
    }

    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     * @return mixed|void
     */
    public function remove(RepeatedDrivingOrder $repeatedDrivingOrder) {
        $this->getEntityManager()->remove($repeatedDrivingOrder);
    }

    public function findAllOrdersForShift(Shift $shift)
    {
        // TODO: Implement findAllOrdersForShift() method.
    }
}