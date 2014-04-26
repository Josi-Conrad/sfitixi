<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 12:30
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;
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

    public function __construct(){
        parent::__construct();
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
     * @return mixed
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
    public function setPassenger($passenger) {
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
    public function setRoute($route) {
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