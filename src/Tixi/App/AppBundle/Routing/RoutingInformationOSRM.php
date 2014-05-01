<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 10:43
 */

namespace Tixi\App\AppBundle\Routing;


use Tixi\App\Routing\RoutingInformation;

class RoutingInformationOSRM extends RoutingInformation{

    private $checksum;

    /**
     * @param mixed $totalDistance
     */
    public function setTotalDistance($totalDistance) {
        $this->totalDistance = $totalDistance;
    }

    /**
     * @param mixed $totalTime
     */
    public function setTotalTime($totalTime) {
        $this->totalTime = $totalTime;
    }

    /**
     * @return mixed
     */
    public function getTotalTime() {
        return $this->totalTime;
    }

    /**
     * @return mixed
     */

    public function getTotalDistance() {
        return $this->totalDistance;
    }

    /**
     * @param mixed $checksum
     */
    public function setChecksum($checksum) {
        $this->checksum = $checksum;
    }

    /**
     * @return mixed
     */
    public function getChecksum() {
        return $this->checksum;
    }

}