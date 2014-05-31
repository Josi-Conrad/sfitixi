<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.05.14
 * Time: 19:17
 */

namespace Tixi\App\AppBundle\Interfaces;


class ZoneTransferDTO {

    const FOUND = 1;
    const NOTFOUND = 0;

    public $status;
    public $zoneId;
    public $zoneName;

    public function toArray() {
        $zoneArray = array();
        $zoneArray['status'] = $this->status;
        if($this->status!==self::NOTFOUND) {
            $zoneArray['zoneid'] = $this->zoneId;
            $zoneArray['zonename'] = $this->zoneName;
        }
        return $zoneArray;
    }

} 