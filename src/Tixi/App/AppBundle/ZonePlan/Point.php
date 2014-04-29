<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 16:51
 */

namespace Tixi\App\AppBundle\ZonePlan;

/**
 * Class Point
 * @package Tixi\App\AppBundle\Util
 */
class Point {
    /**
     * x coordinate of point
     * @var
     */
    public $lat;
    /**
     * y coordinate of point
     * @var
     */
    public $lng;

    /**
     * @param $lat
     * @param $lng
     */
    public function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat) {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng) {
        $this->lng = $lng;
    }

    /**
     * @return mixed
     */
    public function getLng() {
        return $this->lng;
    }

}