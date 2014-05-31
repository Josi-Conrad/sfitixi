<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.05.14
 * Time: 19:17
 */

namespace Tixi\App\AppBundle\Interfaces;


use Tixi\CoreDomain\Zone;

class ZoneAssembler {

    public static function zoneToZoneTransferDTO($zone, $error) {
        $zoneTransferDTO = new ZoneTransferDTO();
        if($error) {
            $zoneTransferDTO->status = ZoneTransferDTO::ERROR;
        }elseif($zone === null) {
            $zoneTransferDTO->status = ZoneTransferDTO::NOTFOUND;
        }else {
            /** @var Zone $zone */
            $zoneTransferDTO->status = ZoneTransferDTO::FOUND;
            $zoneTransferDTO->zoneId = $zone->getId();
            $zoneTransferDTO->zoneName = $zone->getName();
            $zoneTransferDTO->zonePriority = $zone->getPriority();
        }
        return $zoneTransferDTO;
    }

} 