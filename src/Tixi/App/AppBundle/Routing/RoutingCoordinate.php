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
    private $hintLocation;

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
    public function setHintLocation($hintLocation) {
        $this->hintLocation = $hintLocation;
    }

    /**
     * @return mixed
     */
    public function getHintLocation() {
        return $this->hintLocation;
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

        if ($this->hintLocation != null) {
            $ret = $ret . "&hint=" . $this->hintLocation;
        }
        return $ret;
    }
} 