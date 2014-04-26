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
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

/**
 * Class RepeatedDrivingOrderRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Dispo
 */
class RepeatedDrivingOrderRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingOrderRepository {
    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     */
    public function store(RepeatedDrivingOrder $repeatedDrivingOrder) {
        $this->getEntityManager()->persist($repeatedDrivingOrder);
    }

    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     */
    public function remove(RepeatedDrivingOrder $repeatedDrivingOrder) {
        $this->getEntityManager()->remove($repeatedDrivingOrder);
    }
}