<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 12:30
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Shared\CommonBaseEntity;
use Tixi\CoreDomain\Zone;

/**
 * Class DrivingOrderPlan
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingOrderPlanRepositoryDoctrine")
 * @ORM\Table(name="repeated_driving_order_plan")
 */
class RepeatedDrivingOrderPlan extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="date")
     */
    protected $anchorDate;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $endingDate;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $withHolidays;
    /**
     * @ORM\ManyToOne(targetEntity="Route")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id")
     */
    protected $route;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Passenger", inversedBy="repeatedDrivingOrderPlans")
     * @ORM\JoinColumn(name="passenger_id", referencedColumnName="id")
     */
    protected $passenger;
    /**
     * @ORM\OneToMany(targetEntity="RepeatedDrivingOrder", mappedBy="repeatedDrivingOrderPlan")
     * @ORM\JoinColumn(name="repeated_driving_order_id", referencedColumnName="id")
     */
    protected $repeatedDrivingOrders;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     */
    protected $zone;
    /**
     * Number of companies for the traveler within this order.
     * For Example: Passenger needs companion to travel with, so 1 more seat is needed
     * @ORM\Column(type="integer")
     */
    protected $companion;
    /**
     * @ORM\Column(type="text")
     */
    protected $memo;
    /**
     * @ORM\Column(type="integer")
     */
    protected $additionalTime;
    /**
     * @ORM\OneToMany(targetEntity="DrivingOrder", mappedBy="repeatedDrivingOrderPlan")
     */
    protected $drivingOrders;

    protected function __construct() {
        $this->repeatedDrivingOrders = new ArrayCollection();
        $this->drivingOrders = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param \DateTime $anchorDate
     * @param null $withHolidays
     * @param null $companion
     * @param \DateTime $endingDate
     * @param null $memo
     * @param null $additionalTime
     * @return RepeatedDrivingOrderPlan
     */
    public static function registerRepeatedDrivingOrderPlan(\DateTime $anchorDate, $withHolidays = null, $companion = null, \DateTime $endingDate = null, $memo = null, $additionalTime = null) {
        $correctedWithHolidays = (null !== $withHolidays) ? $withHolidays : false;
        $correctedCompanion = (null !== $companion) ? $companion : 0;
        $correctedAdditionalTime = (null !== $additionalTime) ? $additionalTime : 0;
        $correctedEndingDate = ($endingDate !== null) ? $endingDate : DateTimeService::getMaxDateTime();
        $correctedMemo = ($memo !== null) ? $memo : '';
        $rdPlan = new RepeatedDrivingOrderPlan();
        $rdPlan->setAnchorDate($anchorDate);
        $rdPlan->setEndingDate($correctedEndingDate);
        $rdPlan->setWithHolidays($correctedWithHolidays);
        $rdPlan->setCompanion($correctedCompanion);
        $rdPlan->setMemo($correctedMemo);
        $rdPlan->setAdditionalTime($correctedAdditionalTime);
        return $rdPlan;
    }

    /**
     * @param \DateTime $anchorDate
     * @param null $withHolidays
     * @param null $companion
     * @param \DateTime $endingDate
     * @param null $memo
     * @param null $additionalTime
     */
    public function update(\DateTime $anchorDate = null, $withHolidays = null, $companion = null, \DateTime $endingDate = null, $memo = null, $additionalTime = null) {
        $correctedWithHolidays = (null !== $withHolidays) ? $withHolidays : false;
        $correctedCompanion = (null !== $companion) ? $companion : 0;
        $correctedAdditionalTime = (null !== $additionalTime) ? $additionalTime : 0;
        $correctedEndingDate = ($endingDate !== null) ? $endingDate : DateTimeService::getMaxDateTime();
        if(null !== $anchorDate) {
            $this->setAnchorDate($anchorDate);
        }
        if(null !== $memo) {
            $this->setMemo($memo);
        }
        $this->setEndingDate($correctedEndingDate);
        $this->setWithHolidays($correctedWithHolidays);
        $this->setCompanion($correctedCompanion);
        $this->setAdditionalTime($correctedAdditionalTime);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $orders
     */
    public function replaceRepeatedDrivingOrders(ArrayCollection $orders) {
        $this->getRepeatedDrivingOrders()->clear();
        foreach($orders as $order) {
            $this->getRepeatedDrivingOrders()->add($order);
        }
    }

    /**
     * @param RepeatedDrivingOrder $drivingOrder
     */
    public function assignRepeatedDrivingOrder(RepeatedDrivingOrder $drivingOrder){
        $this->getRepeatedDrivingOrders()->add($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function assignDrivingOrder(DrivingOrder $drivingOrder) {
        $this->drivingOrders->add($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function removeDrivingOrder(DrivingOrder $drivingOrder) {
        $this->drivingOrders->removeElement($drivingOrder);
    }

    /**
     * @param Passenger $passenger
     */
    public function assignPassenger(Passenger $passenger) {
        $this->setPassenger($passenger);
    }

    /**
     * removes association to a passenger
     */
    public function removePassenger() {
        $this->setPassenger(null);
    }

    /**
     * @param Route $route
     */
    public function assignRoute(Route $route) {
        $this->setRoute($route);
    }

    public function assignZone(Zone $zone) {
        $this->setZone($zone);
    }

    /**
     * @param mixed $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $companion
     */
    public function setCompanion($companion) {
        $this->companion = $companion;
    }

    /**
     * @return mixed
     */
    public function getCompanion() {
        return $this->companion;
    }

    /**
     * @param mixed $repeatedDrivingOrders
     */
    public function setRepeatedDrivingOrders($repeatedDrivingOrders) {
        $this->repeatedDrivingOrders = $repeatedDrivingOrders;
    }

    /**
     * @return ArrayCollection
     */
    public function getRepeatedDrivingOrders() {
        return $this->repeatedDrivingOrders;
    }

    public function getRepeatedDrivingOrdersAsArray() {
        return $this->repeatedDrivingOrders->toArray();
    }

    /**
     * @param mixed $anchorDate
     */
    public function setAnchorDate($anchorDate) {
        $this->anchorDate = $anchorDate;
    }

    /**
     * @return mixed
     */
    public function getAnchorDate() {
        return $this->anchorDate;
    }

    /**
     * @param mixed $endingDate
     */
    public function setEndingDate($endingDate) {
        $this->endingDate = $endingDate;
    }

    /**
     * @return mixed
     */
    public function getEndingDate() {
        return $this->endingDate;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
    }

    /**
     * @param mixed $passenger
     */
    protected function setPassenger($passenger) {
        $this->passenger = $passenger;
    }

    /**
     * @return mixed
     */
    public function getPassenger() {
        return $this->passenger;
    }

    /**
     * @param mixed $route
     */
    protected function setRoute($route) {
        $this->route = $route;
    }

    /**
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param mixed $withHolidays
     */
    public function setWithHolidays($withHolidays) {
        $this->withHolidays = $withHolidays;
    }

    /**
     * @return mixed
     */
    public function getWithHolidays() {
        return $this->withHolidays;
    }

    /**
     * @param mixed $additionalTime
     */
    public function setAdditionalTime($additionalTime)
    {
        $this->additionalTime = $additionalTime;
    }

    /**
     * @return mixed
     */
    public function getAdditionalTime()
    {
        return $this->additionalTime;
    }



}