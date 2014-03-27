<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class PassengerRegisterDTO extends PersonRegisterDTO {
    //Passenger
    public $isInWheelChair;
    public $isOverweight;
    public $gotMonthlyBilling;
    public $notice;

    //Handicap
    public $handicap;
}