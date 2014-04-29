<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 28.04.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Interfaces\Management\ZonePlanDTO;
use Tixi\CoreDomain\Dispo\ZonePlan;

/**
 * Class ZonePlanAssembler
 * @package Tixi\ApiBundle\Management
 */
class ZonePlanAssembler {
    /**
     * @param ZonePlanDTO $zonePlanDTO
     * @return ZonePlan
     */
    public function dtoToZonePlan(ZonePlanDTO $zonePlanDTO) {
        $zonePlan = ZonePlan::registerZonePlan(
            $zonePlanDTO->innerZone,
            $zonePlanDTO->adjacentZone
        );
        return $zonePlan;
    }

    /**
     * @param ZonePlan $zonePlan
     * @return ZonePlanDTO
     */
    public function zonePlanToDTO(ZonePlan $zonePlan) {
        $zonePlanDTO = new ZonePlanDTO();
        $zonePlanDTO->innerZone = $zonePlan->getInnerZone();
        $zonePlanDTO->adjacentZone = $zonePlan->getAdjacentZone();
        return $zonePlanDTO;
    }
}