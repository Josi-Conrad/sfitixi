<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 12:31
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Class RepeatedDrivingOrder
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingOrderRepositoryDoctrine")
 * @ORM\Table(name="repeateddrivingorder")
 */
class RepeatedDrivingOrder implements DrivingOrderInterface {
    /**
     * Repeated-Order Number
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingOrderPlan", inversedBy="repeatedDrivingOrders")
     * @ORM\JoinColumn(name="repeated_driving_order_plan_id", referencedColumnName="id")
     */
    protected $repeatedDrivingOrderPlan;

    /**
     * @ORM\OneToMany(targetEntity="DrivingOrder", mappedBy="repeatedDrivingOrder")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $pickUpTime;


    protected function __construct() {
        $this->drivingOrders = new ArrayCollection();
    }

    /**
     * @param $weekday
     * @param \DateTime $pickUpTime
     * @return RepeatedDrivingOrder
     */
    public static function registerRepeatedDrivingOrder($weekday, \DateTime $pickUpTime) {
        $rdOrder = new RepeatedDrivingOrder();
        $rdOrder->setWeekday($weekday);
        $rdOrder->setPickUpTime($pickUpTime);
        return $rdOrder;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $orders
     */
    public function replaceDrivingOrders(ArrayCollection $orders) {
        $this->getDrivingOrders()->clear();
        foreach ($orders as $order) {
            $this->assignDrivingOrder($order);
        }
    }

    /**
     * @param mixed $repeatedDrivingOrderPlan
     */
    public function assignRepeatedDrivingOrderPlan($repeatedDrivingOrderPlan) {
        $this->repeatedDrivingOrderPlan = $repeatedDrivingOrderPlan;
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function assignDrivingOrder(DrivingOrder $drivingOrder) {
        $this->getDrivingOrders()->add($drivingOrder);
    }

    /**
     * @param \DateTime $date
     * @return mixed|void
     */
    public function matching(\DateTime $date) {
        // TODO: Implement matching() method.
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param \DateTime $pickUpTime
     */
    public function setPickUpTime(\DateTime $pickUpTime) {
        $this->pickUpTime = $pickUpTime;
    }

    /**
     * @return mixed
     */
    public function getPickUpTime() {
        return $this->pickUpTime;
    }

    /**
     * @return mixed
     */
    public function getRepeatedDrivingOrderPlan() {
        return $this->repeatedDrivingOrderPlan;
    }

    /**
     * @param mixed $weekday
     */
    public function setWeekday($weekday) {
        $this->weekday = $weekday;
    }

    /**
     * @return mixed
     */
    public function getWeekday() {
        return $this->weekday;
    }

    /**
     * @param mixed $drivingOrders
     */
    protected function setDrivingOrders($drivingOrders) {
        $this->drivingOrders = $drivingOrders;
    }

    /**
     * @return ArrayCollection
     */
    public function getDrivingOrders() {
        return $this->drivingOrders;
    }

}