<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Interfaces\Validators as Valid;

/**
 * Class ShiftTypeRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 * @Valid\ShiftTypeRegisterConstraint
 */
class ShiftTypeRegisterDTO {
    public $id;
    public $name;
    public $start;
    public $end;
    public $memo;
}

