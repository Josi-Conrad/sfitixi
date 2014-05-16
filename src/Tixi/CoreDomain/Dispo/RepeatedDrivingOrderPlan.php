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
     * Number of companies for the traveler within this order.
     * For Example: Passenger needs companion to travel with, so 1 more seat is needed
     * @ORM\Column(type="integer")
     */
    protected $companion;
    /**
     * @ORM\Column(type="text")
     */
    protected $memo;

    protected function __construct() {
        $this->repeatedDrivingOrders = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $anchorDate
     * @param $endingDate
     * @param $withHolidays
     * @param int $companion
     * @param null $memo
     * @return RepeatedDrivingOrderPlan
     */
    public static function registerRepeatedDrivingOrderPlan(\DateTime $anchorDate, $withHolidays, $companion = 0, \DateTime $endingDate, $memo = null) {
        $endingDate = ($endingDate !== null) ? $endingDate : DateTimeService::getMaxDateTime();
        $rdPlan = new RepeatedDrivingOrderPlan();
        $rdPlan->setAnchorDate($anchorDate);
        $rdPlan->setEndingDate($endingDate);
        $rdPlan->setWithHolidays($withHolidays);
        $rdPlan->setCompanion($companion);
        $rdPlan->setMemo($memo);
        return $rdPlan;
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
     * @param Passenger $passenger
     */
    public function assignPassenger(Passenger $passenger) {
        $this->setPassenger($passenger);
    }

    public function removePassenger() {
        $this->setPassenger(null);
    }

    /**
     * @param RepeatedDrivingOrder $drivingOrder
     */
    public function assignRepeatedDrivingOrder(RepeatedDrivingOrder $drivingOrder){
        $this->getRepeatedDrivingOrders()->add($drivingOrder);
    }

    /**
     * @param Route $route
     */
    public function assignRoute(Route $route) {
        $this->setRoute($route);
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
     * @return mixed
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

}