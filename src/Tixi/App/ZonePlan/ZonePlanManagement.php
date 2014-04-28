<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 13:40
 */

namespace Tixi\App\ZonePlan;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\ZonePlan;

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

    /**
     * returns true if coordinates of an address matches in predefined adjacent ZonePlan
     * @param $address
     * @return boolean
     */
    public function addressMatchesAdjacentZonePlan(Address $address);

    /**
     * @return ZonePlan
     */
    public function getZonePlan();

    /**
     * @param \Tixi\CoreDomain\Dispo\ZonePlan $zonePlan
     * @return ZonePlan
     */
    public function createOrUpdateZonePlan(ZonePlan $zonePlan);
} 