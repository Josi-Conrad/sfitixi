<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.05.14
 * Time: 19:17
 */

namespace Tixi\App\AppBundle\Interfaces;


class ZoneTransferDTO {

    const ERROR = -1;
    const UNCLASSIFIED = 0;
    const CLASSIFIED = 1;

    public $status;
    public $zoneId;
    public $zoneName;
    public $zonePriority;

    public function toArray() {
        $zoneArray = array();
        $zoneArray['status'] = $this->status;
        if($this->status!==self::ERROR) {
            $zoneArray['zoneid'] = $this->zoneId;
            $zoneArray['zonename'] = $this->zoneName;
            $zoneArray['zonepriority'] = $this->zonePriority;
        }
        return $zoneArray;
    }

} 