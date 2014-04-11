<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 18:40
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\Route
 *
 * Route Entity, with start and target Address as a unique combination
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RouteRepositoryDoctrine")
 * @ORM\Table(name="route",
 * uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"address_start_id", "address_target_id"})}
 * )
 */
class Route {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Address")
     * @ORM\JoinColumn(name="address_start_id", referencedColumnName="id")
     */
    protected $startAddress;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Address")
     * @ORM\JoinColumn(name="address_target_id", referencedColumnName="id")
     */
    protected $targetAddress;
    /**
     * route duration in minutes
     * @ORM\Column(type="integer")
     */
    protected $duration;
    /**
     * distance in m
     * @ORM\Column(type="integer")
     */
    protected $distance;

    private function __construct() {
    }

    /**
     * @param $startAddress
     * @param $targetAddress
     * @param null $duration
     * @param null $distance
     * @return Route
     */
    public static function registerRoute($startAddress, $targetAddress, $duration = null, $distance = null) {
        $route = new Route();
        $route->setStartAddress($startAddress);
        $route->setTargetAddress($targetAddress);
        $route->setDuration($duration);
        $route->setDistance($distance);
        return $route;
    }

    /**
     * @param null $startAddress
     * @param null $targetAddress
     * @param null $duration
     * @param null $distance
     */
    public function updateRouteData($startAddress = null, $targetAddress = null, $duration = null, $distance = null) {
        $this->setChangeDate(new \DateTime('now'));
        $this->setStartAddress($startAddress);
        $this->setTargetAddress($targetAddress);
        $this->setDuration($duration);
        $this->setDistance($distance);
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance) {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getDistance() {
        return $this->distance;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration) {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getDuration() {
        return $this->duration;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $startAddress
     */
    public function setStartAddress($startAddress) {
        $this->startAddress = $startAddress;
    }

    /**
     * @return mixed
     */
    public function getStartAddress() {
        return $this->startAddress;
    }

    /**
     * @param mixed $targetAddress
     */
    public function setTargetAddress($targetAddress) {
        $this->targetAddress = $targetAddress;
    }

    /**
     * @return mixed
     */
    public function getTargetAddress() {
        return $this->targetAddress;
    }
}