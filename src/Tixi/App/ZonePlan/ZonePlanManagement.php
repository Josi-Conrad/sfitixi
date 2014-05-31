<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 13:40
 */

namespace Tixi\App\ZonePlan;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZonePlan;

/**
 * Interface ZonePlanManagement
 * @package Tixi\App\AppBundle
 */
interface ZonePlanManagement {
    /**
     * returns zone which address matches
     * @param $address
     * @return Zone
     */
    public function getZoneForAddress(Address $address);

    /**
     * returns zone which matches city or plz pattern
     * @param $city
     * @param $plz
     * @return Zone
     */
    public function getZoneForAddressData($city, $plz);

    public function getZoneForCity($city);

    public function findOrCreateUnclassfiedZone();
} 