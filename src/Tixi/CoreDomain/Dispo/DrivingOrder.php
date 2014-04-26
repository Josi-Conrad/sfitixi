<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 14:38
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\DrivingOrder
 *
 * @ORM\Entity
 * @ORM\Table(name="driving_order")
 */

class DrivingOrder extends CommonBaseEntity implements DrivingOrderInterface{

    /**
     * Order-Number
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="date")
     */
    protected $pickUpDate;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $pickUpTime;
    /**
     * @ORM\ManyToOne(targetEntity="Route")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id")
     */
    protected $route;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Passenger", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="passenger_id", referencedColumnName="id")
     */
    protected $passenger;
    /**
     * @ORM\ManyToOne(targetEntity="DrivingMission", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="driving_mission_id", referencedColumnName="id")
     */
    protected $drivingMission;
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
     * @param \DateTime $date
     * @return mixed|void
     */
    public function matching(\DateTime $date)
    {
        // TODO: Implement matching() method.
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $passenger
     */
    public function setPassenger($passenger)
    {
        $this->passenger = $passenger;
    }

    /**
     * @return mixed
     */
    public function getPassenger()
    {
        return $this->passenger;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
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
     * @param mixed $pickUpDate
     */
    public function setPickUpDate($pickUpDate) {
        $this->pickUpDate = $pickUpDate;
    }

    /**
     * @return mixed
     */
    public function getPickUpDate() {
        return $this->pickUpDate;
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
     * @param mixed $drivingMission
     */
    public function setDrivingMission($drivingMission) {
        $this->drivingMission = $drivingMission;
    }

    /**
     * @return mixed
     */
    public function getDrivingMission() {
        return $this->drivingMission;
    }
}