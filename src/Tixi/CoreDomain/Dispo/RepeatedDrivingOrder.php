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
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Class RepeatedDrivingOrder
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingOrderRepositoryDoctrine")
 * @ORM\Table(name="repeated_driving_order")
 */
class RepeatedDrivingOrder {

    const OUTWARD_DIRECTION = 0;
    const RETURN_DIRECTION = 1;

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
    /**
     * @ORM\Column(type="smallint")
     */
    protected $direction;


    protected function __construct() {
        $this->drivingOrders = new ArrayCollection();
    }

    /**
     * @param $weekday
     * @param \DateTime $pickUpTime
     * @param null $direction
     * @return RepeatedDrivingOrder
     */
    public static function registerRepeatedDrivingOrder($weekday, \DateTime $pickUpTime, $direction = null) {
        $correctedDirection = (null !== $direction) ? $direction : self::OUTWARD_DIRECTION;
        $rdOrder = new RepeatedDrivingOrder();
        $rdOrder->setWeekday($weekday);
        $rdOrder->setPickUpTime($pickUpTime);
        $rdOrder->setDirection($correctedDirection);
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
        return DateTimeService::getWeekday($date) == $this->getWeekday();
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

    /**
     * @param mixed $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }



}