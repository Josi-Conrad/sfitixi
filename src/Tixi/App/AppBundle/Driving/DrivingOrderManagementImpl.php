<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.06.14
 * Time: 14:04
 */

namespace Tixi\App\AppBundle\Driving;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\Interfaces\DrivingOrderHandleDTO;
use Tixi\App\Driving\DrivingOrderManagement;

class DrivingOrderManagementImpl extends ContainerAware implements DrivingOrderManagement{

    public function handleDrivingOrder(DrivingOrderHandleDTO $handleDTO)
    {
        $test = 'a';
    }
}