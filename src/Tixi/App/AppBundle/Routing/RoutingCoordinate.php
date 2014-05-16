<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 11:09
 */

namespace Tixi\App\AppBundle\Routing;


class RoutingCoordinate {
    private $longitude = 0.0;
    private $latitude = 0.0;
    private $checksum;

    /**
     * @param $lat
     * @param $lng
     */
    public function __construct($lat, $lng) {
        $this->latitude = $lat;
        $this->longitude = $lng;
    }

    /**
     * @param mixed $hintLocation
     */
    public function setChecksum($hintLocation) {
        $this->checksum = $hintLocation;
    }

    /**
     * @return mixed
     */
    public function getChecksum() {
        return $this->checksum;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * @param RoutingCoordinate $otherCoordinate
     * @return bool
     */
    public function equals(RoutingCoordinate $otherCoordinate) {
        if ($otherCoordinate->getLatitude() == $this->latitude &&
            $otherCoordinate->getLongitude() == $this->longitude
        )
            return true;
        return false;
    }

    /**
     * will return correct loc string for API
     * @return string
     */
    public function __toString() {
        $ret = "loc=" . $this->latitude . "," . $this->longitude;

        if ($this->checksum != null) {
            $ret = $ret . "&hint=" . $this->checksum;
        }
        return $ret;
    }
} 