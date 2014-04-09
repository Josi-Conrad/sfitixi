<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;


class VehicleRegisterDTO {
    public $id;
    public $name;
    public $licenceNumber;
    public $dateOfFirstRegistration;
    public $parking;
    public $category;
    public $memo;
    public $managementDetails;
    public $supervisor;
}