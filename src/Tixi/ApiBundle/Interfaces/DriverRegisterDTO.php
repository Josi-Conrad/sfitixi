<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class DriverRegisterDTO extends PersonRegisterDTO {
    //Driver
    public $licenseNumber;
    public $wheelChairAttendance;

    //DriverCategory
    public $driverCategory;

}