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
    /**
     * route information updates on
     * @ORM\Column(type="datetime")
     */
    protected $changeDate;

    private function __construct() {
        $this->changeDate = new \DateTime();
    }

    public static function registerRoute($startAddress, $targetAddress, $duration = null, $distance = null) {
        $route = new Route();
        $route->setStartAddress($startAddress);
        $route->setTargetAddress($targetAddress);
        $route->setDuration($duration);
        $route->setDistance($distance);
        return $route;
    }

    public function updateRouteBasicData($startAddress = null, $targetAddress = null, $duration = null, $distance = null) {
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

    /**
     * @param mixed $changeDate
     */
    public function setChangeDate($changeDate) {
        $this->changeDate = $changeDate;
    }

    /**
     * @return mixed
     */
    public function getChangeDate() {
        return $this->changeDate;
    }

}