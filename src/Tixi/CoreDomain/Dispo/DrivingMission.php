<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\DrivingMission
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\DrivingMissionRepositoryDoctrine")
 * @ORM\Table(name="driving_mission")
 */
class DrivingMission {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="DrivingOrder", mappedBy="drivingMission")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;

    /**
     * @ORM\ManyToOne(targetEntity="DrivingPool", inversedBy="drivingMissions")
     * @ORM\JoinColumn(name="driving_pool_id", referencedColumnName="id")
     */
    protected $drivingPool;

    /**
     * @ORM\Column(type="integer")
     */
    protected $direction;

    /**
     * @ORM\Column(type="integer")
     */
    protected $serviceMinuteOfDay;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $serviceOrder;

    /**
     * @ORM\Column(type="integer")
     */
    protected $serviceDuration;

    /**
     * @ORM\Column(type="integer")
     */
    protected $serviceDistance;


    public function __construct() {
        $this->drivingOrders = new ArrayCollection();
    }

    /**
     * @param $direction
     * @param int $serviceMinuteOfDay
     * @param int $serviceDuration
     * @param int $serviceDistance
     * @return DrivingMission
     */
    public static function registerDrivingMission($direction, $serviceMinuteOfDay = 0, $serviceDuration = 0, $serviceDistance = 0) {
        $drivingMission = new DrivingMission();
        $drivingMission->setDirection($direction);
        $drivingMission->setServiceMinuteOfDay($serviceMinuteOfDay);
        $drivingMission->setServiceDuration($serviceDuration);
        $drivingMission->setServiceDistance($serviceDistance);
        return $drivingMission;
    }

    /**
     * @param DrivingPool $drivingPool
     */
    public function assignDrivingPool(DrivingPool $drivingPool) {
        $this->setDrivingPool($drivingPool);
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function assignDrivingOrder(DrivingOrder $drivingOrder) {
        $this->getDrivingOrders()->add($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function removeDrivingOrder(DrivingOrder $drivingOrder) {
        $this->getDrivingOrders()->removeElement($drivingOrder);
    }

    /**
     * returns earliest pickUpType of orders
     * @return \DateTime
     */
    protected function getAnchorTime() {
    }

    /**
     * returns mission duration in tbd(seconds|minutes)
     * @return mixed
     */
    protected function getDuration() {
    }

    /**
     * @param mixed $drivingOrders
     */
    public function setDrivingOrders($drivingOrders) {
        $this->drivingOrders = $drivingOrders;
    }

    /**
     * @return ArrayCollection
     */
    public function getDrivingOrders() {
        return $this->drivingOrders;
    }

    /**
     * @param mixed $drivingPool
     */
    protected function setDrivingPool($drivingPool) {
        $this->drivingPool = $drivingPool;
    }

    /**
     * @return mixed
     */
    public function getDrivingPool() {
        return $this->drivingPool;
    }

    /**
     * @param mixed $direction
     */
    public function setDirection($direction) {
        $this->direction = $direction;
    }

    /**
     * @return mixed
     */
    public function getDirection() {
        return $this->direction;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $serviceDistance
     */
    public function setServiceDistance($serviceDistance) {
        $this->serviceDistance = $serviceDistance;
    }

    /**
     * @return mixed
     */
    public function getServiceDistance() {
        return $this->serviceDistance;
    }

    /**
     * @param mixed $serviceDuration
     */
    public function setServiceDuration($serviceDuration) {
        $this->serviceDuration = $serviceDuration;
    }

    /**
     * @return mixed
     */
    public function getServiceDuration() {
        return $this->serviceDuration;
    }

    /**
     * @param mixed $serviceMinuteOfDay
     */
    public function setServiceMinuteOfDay($serviceMinuteOfDay) {
        $this->serviceMinuteOfDay = $serviceMinuteOfDay;
    }

    /**
     * @return mixed
     */
    public function getServiceMinuteOfDay() {
        return $this->serviceMinuteOfDay;
    }

    /**
     * @param mixed $serviceOrder
     */
    public function setServiceOrder($serviceOrder) {
        $this->serviceOrder = $serviceOrder;
    }

    /**
     * @return mixed
     */
    public function getServiceOrder() {
        return $this->serviceOrder;
    }

}