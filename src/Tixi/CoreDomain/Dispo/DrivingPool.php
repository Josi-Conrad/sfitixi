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

    /**
     * @param Shift $shift
     */
    public function __construct(Shift $shift) {
        $this->shift = $shift;
        $this->drivingMissions = new ArrayCollection();
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
    public function isCompleted() {
        return ($this->hasAssociatedDriver() && isset($this->vehicle));
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
     * @param mixed $driver
     */
    public function setDriver($driver) {
        $this->driver = $driver;
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
     * @param mixed $shift
     */
    public function setShift($shift) {
        $this->shift = $shift;
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