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
use Tixi\CoreDomain\Vehicle;

/**
 * Tixi\CoreDomain\Dispo\DrivingPool
 *
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
     * @ORM\ManyToOne(targetEntity="WorkingDay", inversedBy="drivingPools")
     * @ORM\JoinColumn(name="working_day_id", referencedColumnName="id")
     */
    protected $workingDay;

    protected $driver;
    protected $vehicle;
    protected $correspondingShift;

    public function __construct(Driver $driver, Shift $shift) {
        $this->driver = $driver;
        $this->correspondingShift = $shift;

        $this->drivingMissions = new ArrayCollection();
    }

    public function isCompleted() {
        return (isset($this->vehicle));
    }

    public function assignVehicle(Vehicle $vehicle) {
        $this->vehicle = $vehicle;
    }

    public function checkCompatibilityForDrivingMission(DrivingMission $drivingMission) {

    }

}