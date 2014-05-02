<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 02.05.14
 * Time: 08:19
 */

namespace Tixi\App\AppBundle\Routing;


use Tixi\CoreDomain\Dispo\Route;

class RoutingClassOSRM {
    protected $route;
    protected $nearestFromLat;
    protected $nearestFromLng;
    protected $nearestToLat;
    protected $nearestToLng;

    /**
     * @param Route $route
     */
    public function __construct(Route &$route){
        $this->setRoute($route);
    }
    /**
     * @param mixed $nearestFromLat
     */
    public function setNearestFromLat($nearestFromLat) {
        $this->nearestFromLat = $nearestFromLat;
    }

    /**
     * @return mixed
     */
    public function getNearestFromLat() {
        return $this->nearestFromLat;
    }

    /**
     * @param mixed $nearestFromLng
     */
    public function setNearestFromLng($nearestFromLng) {
        $this->nearestFromLng = $nearestFromLng;
    }

    /**
     * @return mixed
     */
    public function getNearestFromLng() {
        return $this->nearestFromLng;
    }

    /**
     * @param mixed $nearestToLat
     */
    public function setNearestToLat($nearestToLat) {
        $this->nearestToLat = $nearestToLat;
    }

    /**
     * @return mixed
     */
    public function getNearestToLat() {
        return $this->nearestToLat;
    }

    /**
     * @param mixed $nearestToLng
     */
    public function setNearestToLng($nearestToLng) {
        $this->nearestToLng = $nearestToLng;
    }

    /**
     * @return mixed
     */
    public function getNearestToLng() {
        return $this->nearestToLng;
    }

    /**
     * @param mixed $route
     */
    public function setRoute(Route $route) {
        $this->route = $route;
    }

    /**
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }


}