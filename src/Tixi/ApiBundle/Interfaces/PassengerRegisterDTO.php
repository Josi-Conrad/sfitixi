<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PassengerRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class PassengerRegisterDTO extends PersonRegisterDTO {
    //Passenger
    public $isInWheelChair;
    public $gotMonthlyBilling;
    public $notice;

    public $handicaps;
    public $insurances;

}