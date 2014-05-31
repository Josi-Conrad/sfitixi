<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:48
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Zone;

/**
 * Class ZoneAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class ZoneAssembler {

    /**
     * @param ZoneRegisterDTO $dto
     * @return Zone
     */
    public function registerDTOtoNewZone(ZoneRegisterDTO $dto) {
        $zone = Zone::registerZone($dto->name);
        return $zone;
    }

    /**
     * @param Zone $zone
     * @param ZoneRegisterDTO $dto
     */
    public function registerDTOtoZone(Zone $zone, ZoneRegisterDTO $dto) {
        $zone->updateZone($dto->name);
    }

    /**
     * @param Zone $zone
     * @return ZoneRegisterDTO
     */
    public function toZoneRegisterDTO(Zone $zone) {
        $zoneDTO = new ZoneRegisterDTO();
        $zoneDTO->id = $zone->getId();
        $zoneDTO->name = $zone->getName();
        return $zoneDTO;
    }

    /**
     * @param $zones
     * @return array
     */
    public function zonesToZoneListDTOs($zones) {
        $dtoArray = array();
        foreach ($zones as $zone) {
            $dtoArray[] = $this->toZoneListDTO($zone);
        }
        return $dtoArray;
    }

    /**
     * @param Zone $zone
     * @return ZoneListDTO
     */
    public function toZoneListDTO(Zone $zone) {
        $zoneListDTO = new ZoneListDTO();
        $zoneListDTO->id = $zone->getId();
        $zoneListDTO->name = $zone->getName();
        return $zoneListDTO;
    }
}