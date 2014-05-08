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
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Driver", inversedBy="drivingPools")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    protected $driver;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    protected $vehicle;

    public function __construct() {
        $this->drivingMissions = new ArrayCollection();
    }

    /**
     * @param Shift $shift
     * @return DrivingPool
     */
    public static function registerDrivingPool(Shift $shift) {
        $drivingPool = new DrivingPool();
        $drivingPool->assignShift($shift);
        return $drivingPool;
    }


    /**
     * @return bool
     */
    public function hasAssociatedDriver() {
        return isset($this->driver);
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
    public function isCompleted() {
        return ($this->hasAssociatedDriver() && $this->hasAssociatedVehicle());
    }

    /**
     * @param mixed $shift
     */
    public function assignShift($shift) {
        $this->shift = $shift;
    }
    /**
     * @param Driver $driver
     */
    public function assignDriver(Driver $driver) {
        $this->driver = $driver;
    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignVehicle(Vehicle $vehicle) {
        $this->vehicle = $vehicle;
    }

    /**
     * @param DrivingMission $drivingMission
     */
    public function checkCompatibilityForDrivingMission(DrivingMission $drivingMission) {

    }

    /**
     * @return mixed
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @param mixed $drivingMissions
     */
    public function setDrivingMissions($drivingMissions) {
        $this->drivingMissions = $drivingMissions;
    }

    /**
     * @return mixed
     */
    public function getDrivingMissions() {
        return $this->drivingMissions;
    }

    /**
     * @return mixed
     */
    public function getShift() {
        return $this->shift;
    }

    /**
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle) {
        $this->vehicle = $vehicle;
    }

    /**
     * @return mixed
     */
    public function getVehicle() {
        return $this->vehicle;
    }

}