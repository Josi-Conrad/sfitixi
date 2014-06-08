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
     * @param $cities
     * @return null|Zone
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getZoneWithHighestPriorityForCities($cities);

    /**
     * @param $city
     * @return null|Zone
     * @throws \InvalidArgumentException
     */
    public function getZoneForCity($city);

    /**
     * @return Zone
     */
    public function findOrCreateUnclassfiedZone();

    /**
     * @deprecated
     * returns zone which matches city or plz pattern
     * @param $city
     * @param $plz
     * @return Zone
     */
    public function getZoneForAddressData($city, $plz);
} 