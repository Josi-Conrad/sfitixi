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

interface DrivingOrderManagement {

    public function handleDrivingOrder(DrivingOrderHandleDTO $handleDTO);

}