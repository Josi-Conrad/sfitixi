<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class AddressRegisterDTO {
    //Address
    public $name;
    public $street;
    /**
     * @Assert\Length(min = "4", max = "6",
     * minMessage="postal.code.min", maxMessage="postal.code.max")
     */
    public $postalCode;
    public $city;
    public $country;
    public $lng;
    public $lat;
    public $type;
}