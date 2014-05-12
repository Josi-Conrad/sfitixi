<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Interfaces\Validators as Valid;
/**
 * Class PersonRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 * @Valid\PersonRegisterConstraint
 */
class PersonRegisterDTO {
    //Person
    public $person_id;
    public $gender;
    public $title;
    public $firstname;
    public $lastname;
    public $telephone;
    public $fax;
    public $email;
    public $entryDate;
    public $birthday;
    public $extraMinutes;
    public $details;
    public $contradictVehicleCategories;

    public $correspondenceAddress;
    public $billingAddress;
    public $isBillingAddress;

    public $lookaheadaddress;

    public function __construct(){
        $this->gender = 'm';
        $this->isBillingAddress = true;
    }
}

