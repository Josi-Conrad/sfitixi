<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:37
 */

namespace Tixi\App\Driving;


use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderRegisterDTO;
use Tixi\App\AppBundle\Interfaces\DrivingOrderHandleDTO;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;

interface DrivingOrderManagement {
    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed
     */
    public function handleNewDrivingOrder(DrivingOrder $drivingOrder);

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed
     */
    public function handleDeletionOfDrivingOrder(DrivingOrder $drivingOrder);

    /**
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     * @return mixed
     */
    public function handleNewRepeatedDrivingOrder(RepeatedDrivingOrderPlan $drivingOrderPlan);

}