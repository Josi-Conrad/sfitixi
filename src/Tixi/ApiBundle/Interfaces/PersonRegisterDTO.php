<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class PersonRegisterDTO extends AddressRegisterDTO{
    //Person
    public $id;
    public $isActive;
    public $title;
    public $firstname;
    public $lastname;
    /**
     * @Assert\Regex(pattern="/^[\+0-9 ]{5,19}$/", message="telephone.nr.invalid")
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
}