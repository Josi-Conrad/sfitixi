<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class PassengerRegisterDTO {
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

    //Passenger
    public $isInWheelChair;
    public $isOverweight;
    public $gotMonthlyBilling;
    public $notice;

    //Handicap
    public $handicap;

    //Addresses
    public $street;
    public $postalCode;
    public $city;
    public $country;
}