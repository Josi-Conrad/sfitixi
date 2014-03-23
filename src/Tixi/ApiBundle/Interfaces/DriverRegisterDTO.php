<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class DriverRegisterDTO {
    //Person
    public $id;
    public $isActive;
    public $title;
    public $firstname;
    public $lastname;
    public $telephone;
    public $email;
    public $entryDate;
    public $birthday;
    public $extraMinutes;
    public $details;

    //Driver
    public $licenseNumber;
    public $wheelChairAttendance;

    //DriverCategory
    public $driverCategory;

    //Addresses
    public $street;
    public $postalCode;
    public $city;
    public $country;
}