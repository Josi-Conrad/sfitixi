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
 * @ORM\Entity
 * @ORM\Table(name="repeateddrivingorder")
 */

class RepeatedDrivingOrder extends CommonBaseEntity implements DrivingOrderInterface{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingOrderPlan", inversedBy="repeatedOrders")
     * @ORM\JoinColumn(name="passenger_id", referencedColumnName="id")
     */
    protected $orderPlan;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $pickUpTime;


    public function matching(\DateTime $date)
    {
        // TODO: Implement matching() method.
    }

    /**
     * @param mixed $orderPlan
     */
    public function setOrderPlan($orderPlan)
    {
        $this->orderPlan = $orderPlan;
    }

    /**
     * @return mixed
     */
    public function getOrderPlan()
    {
        return $this->orderPlan;
    }

    /**
     * @param mixed $weekday
     */
    public function setWeekday($weekday)
    {
        $this->weekday = $weekday;
    }

    /**
     * @return mixed
     */
    public function getWeekday()
    {
        return $this->weekday;
    }


}