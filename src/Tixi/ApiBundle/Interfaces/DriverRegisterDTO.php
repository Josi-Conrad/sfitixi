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
    /**
     * @Assert\Regex(pattern="/^[\+0-9 ]{5,15}$/", message="telephone.nr.invalid")
     */
    public $telephone;
    /**
     * @Assert\Email(message="email.invalid")
     */
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
    /**
     * @Assert\Length(min = "4", max = "6",
     * minMessage="postal.code.min", maxMessage="postal.code.max")
     */
    public $postalCode;
    public $city;
    public $country;
}