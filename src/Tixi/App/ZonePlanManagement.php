<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 13:40
 */

namespace Tixi\App;
use Tixi\CoreDomain\Address;

/**
 * Interface ZonePlanManagement
 * @package Tixi\App\AppBundle
 */
interface ZonePlanManagement {
    /**
     * returns true if coordinates of an address matches in predefined ZonePlan
     * @param $address
     * @return boolean
     */
    public function addressMatchesZonePlan(Address $address);

} 