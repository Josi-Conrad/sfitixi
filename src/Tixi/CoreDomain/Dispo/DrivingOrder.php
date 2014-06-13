<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 14:38
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Shared\CommonBaseEntity;
use Tixi\CoreDomain\Zone;

/**
 * Tixi\CoreDomain\Dispo\DrivingOrder
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\DrivingOrderRepositoryDoctrine")
 * @ORM\Table(name="driving_order")
 */
class DrivingOrder extends CommonBaseEntity {
    /** status of a drivingOrder */
    const PENDENT = 0;
    const COMPLETED = 1;
    const CANCELED = 2;

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
     * @var $passenger Passenger
     */
    protected $passenger;
    /**
     * @ORM\ManyToOne(targetEntity="DrivingMission", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="driving_mission_id", referencedColumnName="id")
     */
    protected $drivingMission;
    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingOrder", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="repeated_driving_order_id", referencedColumnName="id")
     */
    protected $repeatedDrivingOrder;
    /**
     * @ORM\OneToOne(targetEntity="DrivingOrder")
     */
    protected $correspondingReturnOrder;
    /**
     * @ORM\OneToOne(targetEntity="DrivingOrder")
     */
    protected $correspondingOutwardOrder;
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $manualRoute;
    /**
     * @ORM\Column(type="integer")
     */
    protected $additionalTime;
    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingOrderPlan", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="repeateddrivingassertionplan_id", referencedColumnName="id")
     */
    protected $repeatedDrivingOrderPlan;


    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param Passenger $passenger
     * @param $pickupDate
     * @param $pickupTime
     * @param null $companion
     * @param null $memo
     * @param int $status
     * @param bool $manualRoute
     * @param null $additionalTime
     * @return DrivingOrder
     */
    public static function registerDrivingOrder(Passenger $passenger, $pickupDate, $pickupTime, $companion = null, $memo = null, $status = self::PENDENT, $manualRoute = false, $additionalTime = null) {
        $correctedCompanion = (null !== $companion) ? $companion : 0;
        $correctedAdditionalTime = (null !== $additionalTime) ? $additionalTime : 0;
        $drivingOrder = new DrivingOrder();
        $drivingOrder->assignPassenger($passenger);
        $drivingOrder->setPickUpDate($pickupDate);
        $drivingOrder->setPickUpTime($pickupTime);
        $drivingOrder->setCompanion($correctedCompanion);
        $drivingOrder->setStatus($status);
        $drivingOrder->setManualRoute($manualRoute);
        $drivingOrder->setMemo($memo);
        $drivingOrder->setAdditionalTime($correctedAdditionalTime);
        return $drivingOrder;
    }

    /**
     * @param null $memo
     * @param null $status
     */
    public function update($memo = null, $status = null) {
        if (null !== $memo) {
            $this->setMemo($memo);
        }
        if (null !== $status) {
            $this->setStatus($status);
        }
        $this->updateModifiedDate();
    }

    /**
     * @throws \LogicException
     */
    public function deletePhysically() {
        if (null !== $this->drivingMission) {
            throw new \LogicException('please delete corresponding driving mission first');
        }
        /**@var $this ->passenger Passenger */
        $this->getPassenger()->removeDrivingOrder($this);
        if (!empty($this->correspondingReturnOrder)) {
            $this->getCorrespondingReturnOrder()->removeOutwardOrder();
        }
        if (!empty($this->correspondingOutwardOrder)) {
            $this->getCorrespondingOutwardOrder()->removeReturnOrder();
        }
    }

    /**
     * @return array
     */
    public static function getStatusArray() {
        return array(
            self::PENDENT => 'drivingorder.status.pendent',
            self::COMPLETED => 'drivingorder.status.completed',
            self::CANCELED => 'drivingorder.status.canceled'
        );
    }

    /**
     * @param RepeatedDrivingOrder $repeatedDrivingOrder
     */
    public function assignRepeatedDrivingOrder(RepeatedDrivingOrder $repeatedDrivingOrder) {
        $this->setRepeatedDrivingOrder($repeatedDrivingOrder);
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     */
    public function assignRepeatedDrivingOrderPlan(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan) {
        $this->repeatedDrivingOrderPlan = $repeatedDrivingOrderPlan;
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     */
    public function removeRepeatedDrivingOrderPlan(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan) {
        $this->repeatedDrivingOrderPlan = null;
    }


    /**
     * @param DrivingMission $drivingMission
     */
    public function assignDrivingMission(DrivingMission $drivingMission) {
        $this->setDrivingMission($drivingMission);
    }

    /**
     * remove drivingMission association
     */
    public function removeDrivingMission() {
        $this->drivingMission = null;
    }

    /**
     * @param Passenger $passenger
     */
    public function assignPassenger(Passenger $passenger) {
        $this->setPassenger($passenger);
    }

    /**
     * @param DrivingOrder $returnOrder
     */
    public function assignReturnOrder(DrivingOrder $returnOrder) {
        $this->correspondingReturnOrder = $returnOrder;
        $returnOrder->correspondingOutwardOrder = $this;
    }

    /**
     * removes self-referenced association
     */
    public function removeOutwardOrder() {
        $this->correspondingOutwardOrder = null;
    }

    /**
     * removes  self-referenced association
     */
    public function removeReturnOrder() {
        $this->correspondingReturnOrder = null;
    }

    /**
     * @param Zone $zone
     */
    public function assignZone(Zone $zone) {
        $this->zone = $zone;
    }

    /**
     * @return Zone
     */
    public function getZone() {
        return $this->zone;
    }

    /**
     * @param mixed $drivingMission
     */
    protected function setDrivingMission($drivingMission) {
        $this->drivingMission = $drivingMission;
    }

    /**
     * @param mixed $passenger
     */
    protected function setPassenger($passenger) {
        $this->passenger = $passenger;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route) {
        $this->route = $route;
    }

    /**
     * @param mixed $repeatedDrivingOrder
     */
    protected function setRepeatedDrivingOrder($repeatedDrivingOrder) {
        $this->repeatedDrivingOrder = $repeatedDrivingOrder;
    }

    /**
     * @return mixed
     */
    public function getRepeatedDrivingOrder() {
        return $this->repeatedDrivingOrder;
    }

    /**
     * @param \DateTime $date
     * @return mixed|void
     */
    public function matching(\DateTime $date) {
        //implement if necessary to check if this orders matches a certain DateTime
    }

    /**
     * @param Route $route
     */
    public function assignRoute(Route $route) {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Passenger
     */
    public function getPassenger() {
        return $this->passenger;
    }

    /**
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param mixed $companion
     */
    public function setCompanion($companion) {
        $this->companion = $companion;
    }

    /**
     * @return integer
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
     * @return \DateTime
     */
    public function getPickUpTime() {
        return $this->pickUpTime;
    }

    /**
     * @return DrivingMission | null
     */
    public function getDrivingMission() {
        return $this->drivingMission;
    }

    /**
     * @param mixed $manualRoute
     */
    public function setManualRoute($manualRoute) {
        $this->manualRoute = $manualRoute;
    }

    /**
     * @return mixed
     */
    public function getManualRoute() {
        return $this->manualRoute;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $additionalTime
     */
    public function setAdditionalTime($additionalTime) {
        $this->additionalTime = $additionalTime;
    }

    /**
     * @return mixed
     */
    public function getAdditionalTime() {
        return $this->additionalTime;
    }

    /**
     * @param DrivingOrder $correspondingOutwardOrder
     */
    public function setCorrespondingOutwardOrder($correspondingOutwardOrder) {
        $this->correspondingOutwardOrder = $correspondingOutwardOrder;
    }

    /**
     * @param DrivingOrder $correspondingReturnOrder
     */
    public function setCorrespondingReturnOrder($correspondingReturnOrder) {
        $this->correspondingReturnOrder = $correspondingReturnOrder;
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     */
    public function setRepeatedDrivingOrderPlan($repeatedDrivingOrderPlan) {
        $this->repeatedDrivingOrderPlan = $repeatedDrivingOrderPlan;
    }

    /**
     * @return DrivingOrder
     */
    public function getCorrespondingOutwardOrder() {
        return $this->correspondingOutwardOrder;
    }

    /**
     * @return DrivingOrder
     */
    public function getCorrespondingReturnOrder() {
        return $this->correspondingReturnOrder;
    }

    /**
     * @return RepeatedDrivingOrderPlan
     */
    public function getRepeatedDrivingOrderPlan() {
        return $this->repeatedDrivingOrderPlan;
    }

}