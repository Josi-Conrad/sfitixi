<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;
use Tixi\ApiBundle\Interfaces\AddressRegisterDTO;

/**
 * Class VehicleDepotRegisterDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class VehicleDepotRegisterDTO {
    public $id;
    public $name;
    public $memo;
    public $address;
}