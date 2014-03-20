<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class VehicleRegisterDTO {

    public $id;

    /**
     * @Assert\NotBlank(message = "vehicle.name.not_blank")
     */
    public $name;

    /**
     * @Assert\NotBlank(message = "vehicle.nr.not_blank")
     * @Assert\Regex(pattern="/\d+/", message = "vehicle.nr.not_nr")
     */
    public $licenceNumber;
    public $dateOfFirstRegistration;
    public $parkingLotNumber;
    public $vehicleCategory;
}