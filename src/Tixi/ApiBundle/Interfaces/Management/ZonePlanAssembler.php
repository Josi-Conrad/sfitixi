<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:48
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\ZonePlan;

/**
 * Class ZonePlanAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class ZonePlanAssembler {

    /**
     * @param ZonePlanRegisterDTO $dto
     * @return ZonePlan
     */
    public function registerDTOtoNewZonePlan(ZonePlanRegisterDTO $dto) {
        $zonePlan = ZonePlan::registerZonePlan($dto->city, $dto->postalCode, $dto->memo);
        $zonePlan->setZone($dto->zone);
        return $zonePlan;
    }

    /**
     * @param ZonePlan $zonePlan
     * @param ZonePlanRegisterDTO $dto
     */
    public function registerDTOtoZonePlan(ZonePlan $zonePlan, ZonePlanRegisterDTO $dto) {
        $zonePlan->updateZonePlan($dto->city, $dto->postalCode, $dto->memo);
        $zonePlan->setZone($dto->zone);
    }

    /**
     * @param ZonePlan $zonePlan
     * @return ZonePlanRegisterDTO
     */
    public function toZonePlanRegisterDTO(ZonePlan $zonePlan) {
        $zonePlanDTO = new ZonePlanRegisterDTO();
        $zonePlanDTO->id = $zonePlan->getId();
        $zonePlanDTO->city = $zonePlan->getCity();
        $zonePlanDTO->postalCode = $zonePlan->getPostalCode();
        $zonePlanDTO->memo = $zonePlan->getMemo();
        $zonePlanDTO->zone = $zonePlan->getZone();
        return $zonePlanDTO;
    }

    /**
     * @param $zonePlans
     * @return array
     */
    public function zonePlansToZonePlanListDTOs($zonePlans) {
        $dtoArray = array();
        foreach ($zonePlans as $zonePlan) {
            $dtoArray[] = $this->toZonePlanListDTO($zonePlan);
        }
        return $dtoArray;
    }

    /**
     * @param ZonePlan $zonePlan
     * @return ZonePlanListDTO
     */
    public function toZonePlanListDTO(ZonePlan $zonePlan) {
        $zonePlanListDTO = new ZonePlanListDTO();
        $zonePlanListDTO->id = $zonePlan->getId();
        $zonePlanListDTO->city = $zonePlan->getCity();
        $zonePlanListDTO->postalCode = $zonePlan->getPostalCode();
        $zonePlanListDTO->zoneName = $zonePlan->getZone()->getName();
        return $zonePlanListDTO;
    }
}