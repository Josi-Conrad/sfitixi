<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 21.03.14
 * Time: 14:59
 */

namespace Tixi\App\AppBundle\Interfaces;

/**
 * Class AddressHandleDTO
 * @package Tixi\App\AppBundle\Interfaces
 */
class AddressHandleDTO {

    public $id;
    public $displayName;
    public $street;
    public $postalCode;
    public $city;
    public $country;
    public $lat;
    public $lng;
    public $source;
} 