<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;
use Tixi\CoreDomain\Vehicle;

/**
 * Tixi\CoreDomain\Dispo\DrivingPool
 * Assign driver to a vehicle, shift and mission
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\DrivingPoolRepositoryDoctrine")
 * @ORM\Table(name="driving_pool")
 */
class DrivingPool {
    /** status on the drivingPool */
    const CREATED = 0;
    const WAITING_FOR_CONFIRMATION = 1;
    const CONFIRMED = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\OneToMany(targetEntity="DrivingMission", mappedBy="drivingPool")
     * @ORM\JoinColumn(name="driving_mission_id", referencedColumnName="id")
     */
    protected $drivingMissions;
    /**
     * @ORM\ManyToOne(targetEntity="Shift", inversedBy="drivingPools")
     * @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     */
    protected $shift;

    /**
     * @ORM\OneToOne(targetEntity="DrivingAssertion", inversedBy="drivingPool")
     * @ORM\JoinColumn(name="driving_assertion_id", referencedColumnName="id")
     */
    protected $drivingAssertion;
//    /**
//     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Driver", inversedBy="drivingPools")
//     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
//     */
//    protected $driver;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    protected $vehicle;
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    public function __construct() {
        $this->drivingMissions = new ArrayCollection();
    }

    /**
     * @param Shift $shift
     * @param int $status
     * @return DrivingPool
     */
    public static function registerDrivingPool(Shift $shift, $status = self::CREATED) {
        $drivingPool = new DrivingPool();
        $drivingPool->assignShift($shift);
        $drivingPool->setStatus($status);
        return $drivingPool;
    }


    /**
     * @return bool
     */
    public function hasAssociatedDriver() {
        return isset($this->drivingAssertion);
    }

    /**
     * @return bool
     */
    public function hasAssociatedVehicle() {
        return isset($this->vehicle);
    }

    /**
     * @return bool
     */
    public function hasAssociatedDrivingMissions() {
        return (count($this->getDrivingMissions()) > 0);
    }

    /**
     * @return bool
     */
    public function isCompleted() {
        return ($this->hasAssociatedDriver() && $this->hasAssociatedVehicle());
    }

    public function getAmountOfAssociatedDrivingMissions() {
        return count($this->drivingMissions);
    }

    /**
     * @param mixed $shift
     */
    public function assignShift($shift) {
        $this->shift = $shift;
    }

    /**
     * @param DrivingMission $drivingMission
     */
    public function assignDrivingMission(DrivingMission $drivingMission) {
        $this->drivingMissions->add($drivingMission);
    }

    /**
     * @param DrivingMission $drivingMission
     */
    public function removeDrivingMission(DrivingMission $drivingMission) {
        $this->drivingMissions->removeElement($drivingMission);
    }

    /**
     * removes all associations from missions * <-> 1 orders
     */
    public function removeDrivingMissions() {
        foreach ($this->drivingMissions as $mission) {
            /**@var $mission DrivingMission */
            $mission->removeDrivingPool();
        }
        $this->drivingMissions->clear();
    }

//    /**
//     * @param Driver $driver
//     */
//    public function assignDriver(Driver $driver) {
//        $this->driver = $driver;
//    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignVehicle(Vehicle $vehicle) {
        $this->vehicle = $vehicle;
    }

    public function removeVehicle(){
        $this->vehicle = null;
    }

    public function assignDrivingAssertion(DrivingAssertion $drivingAssertion) {
        $this->drivingAssertion = $drivingAssertion;
    }

    public function removeDrivingAssertion() {
        $this->drivingAssertion = null;
    }

    /**
     * @param DrivingMission $drivingMission
     */
    public function checkCompatibilityForDrivingMission(DrivingMission $drivingMission) {

    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Driver
     */
    public function getDriver() {
        $driver = null;
        if (isset($this->drivingAssertion) && null !== $this->drivingAssertion) {
            $driver = $this->drivingAssertion->getDriver();
        }
        return $driver;
    }

    /**
     * @param mixed $drivingMissions
     */
    public function setDrivingMissions($drivingMissions) {
        $this->drivingMissions = $drivingMissions;
    }

    /**
     * @return DrivingMission[]
     */
    public function getDrivingMissions() {
        return $this->drivingMissions;
    }

    /**
     * @return Shift
     */
    public function getShift() {
        return $this->shift;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle() {
        return $this->vehicle;
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

}