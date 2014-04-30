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
use Tixi\CoreDomain\Shared\CommonBaseEntity;

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
class Route extends CommonBaseEntity {
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
    /**
     * additional time taken for this route
     * This is a special case, if routing time is manually corrected by user
     * @ORM\Column(type="integer")
     */
    protected $additionalTime;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $startAddress
     * @param $targetAddress
     * @param null $duration
     * @param null $distance
     * @param int $additionalTime
     * @return Route
     */
    public static function registerRoute($startAddress, $targetAddress, $duration = null, $distance = null, $additionalTime = 0) {
        $route = new Route();
        $route->setStartAddress($startAddress);
        $route->setTargetAddress($targetAddress);
        $route->setDuration($duration);
        $route->setDistance($distance);
        $route->setAdditionalTime($additionalTime);
        return $route;
    }

    /**
     * @param null $startAddress
     * @param null $targetAddress
     * @param null $duration
     * @param null $distance
     */
    public function updateRouteData($startAddress = null, $targetAddress = null, $duration = null, $distance = null) {
        $this->setStartAddress($startAddress);
        $this->setTargetAddress($targetAddress);
        $this->setDuration($duration);
        $this->setDistance($distance);
        parent::updateModifiedDate();
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

    /**
     * @param mixed $additionalTime
     */
    public function setAdditionalTime($additionalTime) {
        $this->additionalTime = $additionalTime;
    }

    /**
     * @return mixed
     */
    public function getAdditionalTime() {
        return $this->additionalTime;
    }
}