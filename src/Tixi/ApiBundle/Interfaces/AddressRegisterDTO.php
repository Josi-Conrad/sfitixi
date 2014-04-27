<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AddressRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class AddressRegisterDTO {
    //Address
    public $address_id;
    public $address_name;
    public $street;
    public $postalCode;
    public $city;
    public $country;
    public $lng;
    public $lat;
    public $source;
}