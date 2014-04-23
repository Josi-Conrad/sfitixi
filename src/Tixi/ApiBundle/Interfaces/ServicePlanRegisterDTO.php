<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Interfaces\Validators as Valid;
/**
 * Class ServicePlanRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 * @Valid\ServicePlanRegisterConstraint
 */
class ServicePlanRegisterDTO {
    public $id;
    public $start;
    public $end;
    public $memo;
}

