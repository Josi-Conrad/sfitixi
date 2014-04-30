<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 12:31
 */

namespace Tixi\CoreDomain\Dispo;

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
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $pickUpTime;

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
     * @param mixed $pickUpTime
     */
    public function setPickUpTime($pickUpTime) {
        $this->pickUpTime = $pickUpTime;
    }

    /**
     * @return mixed
     */
    public function getPickUpTime() {
        return $this->pickUpTime;
    }

    /**
     * @param mixed $repeatedDrivingOrderPlan
     */
    public function setRepeatedDrivingOrderPlan($repeatedDrivingOrderPlan) {
        $this->repeatedDrivingOrderPlan = $repeatedDrivingOrderPlan;
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

}