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

    public static function zoneToZoneTransferDTO($zone, $error, $translator) {
        $zoneTransferDTO = new ZoneTransferDTO();
        if($error) {
            $zoneTransferDTO->status = ZoneTransferDTO::ERROR;
        }
        else {
            /** @var Zone $zone */
            if($zone->isUnclassified()) {
                $zoneTransferDTO->status = ZoneTransferDTO::UNCLASSIFIED;
            }else {
                $zoneTransferDTO->status =ZoneTransferDTO::CLASSIFIED;
            }
            $zoneTransferDTO->zoneId = $zone->getId();
            $zoneTransferDTO->zoneName = $translator->trans($zone->getName());
            $zoneTransferDTO->zonePriority = $zone->getPriority();
        }
        return $zoneTransferDTO;
    }

} 