<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DriverRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class DriverRegisterDTO extends PersonRegisterDTO {
    //Driver
    public $licenceNumber;
    public $wheelChairAttendance;

    //DriverCategory
    public $driverCategory;

}