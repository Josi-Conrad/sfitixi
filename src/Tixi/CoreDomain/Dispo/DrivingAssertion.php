<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:52
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Class DrivingAssertion
 * @package Tixi\CoreDomain\Dispo
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\DrivingAssertionRepositoryDoctrine")
 * @ORM\Table(name="driving_assertion")
 */
class DrivingAssertion extends CommonBaseEntity implements DrivingAssertionInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Driver", inversedBy="drivingAssertions")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     * @var $driver Driver
     */
    protected $driver;
    /**
     * @ORM\ManyToOne(targetEntity="Shift", inversedBy="drivingAssertions")
     * @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     * @var $shift Shift
     */
    protected $shift;
    /**
     * @ORM\OneToOne(targetEntity="DrivingPool", mappedBy="drivingAssertion")
     * @var $drivingPool DrivingPool
     */
    protected $drivingPool;
    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingAssertionPlan", inversedBy="drivingAssertions")
     * @ORM\JoinColumn(name="repeateddrivingassertionplan_id", referencedColumnName="id")
     */
    protected $repeatedDrivingAssertionPlan;

    /**
     * @param Shift $shift
     * @return mixed
     */
    public function matching(Shift $shift) {
        return $shift->getId() === $this->shift->getId();
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function matchingDateTime(\DateTime $dateTime) {
        //to be implemented if necessary
    }

    /**
     * @param Driver $driver
     * @param Shift $shift
     * @return DrivingAssertion
     */
    public static function registerDrivingAssertion(Driver $driver, Shift $shift) {
        $drivingAssertion = new DrivingAssertion();
        $drivingAssertion->assignShift($shift);
        $drivingAssertion->assignDriver($driver);
        return $drivingAssertion;
    }

    /**
     * deletes this drivingAssertion physically, for example if production plan changes
     */
    public function deletePhysically() {
        /**@var $this->driver Driver */
        $this->driver->removeDrivingAssertion($this);
        $this->shift->removeDrivingAssertion($this);
        if (null !== $this->drivingPool) {
            /**@var $this->drivingPool DrivingPool */
            $this->drivingPool->removeDrivingAssertion();
        }
    }

    /**
     * @param Driver $driver
     */
    public function assignDriver(Driver $driver) {
        $this->driver = $driver;
        $driver->assignDrivingAssertion($this);
    }

    /**
     * @param Shift $shift
     */
    public function assignShift(Shift $shift) {
        $this->shift = $shift;
        $shift->assignDrivingAssertion($this);
    }

    /**
     * @param DrivingPool $drivingPool
     */
    public function assignDrivingPool(DrivingPool $drivingPool) {
        $this->drivingPool = $drivingPool;
        $drivingPool->assignDrivingAssertion($this);
    }

    /**
     * @return bool
     */
    public function isAssignedToDrivingPool() {
        return (isset($this->drivingPool) && null !== $this->drivingPool);
    }

    /**
     * @param RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan
     */
    public function assignedRepeatedDrivingAssertionPlan(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan) {
        $this->repeatedDrivingAssertionPlan = $repeatedDrivingAssertionPlan;
    }

    /**
     * removes corresponding repeatedDrivingAssertionPlan from creation process for repeated
     */
    public function removeRepeatedDrivingAssertionPlan() {
        $this->repeatedDrivingAssertionPlan = null;
    }

    /**
     * @return mixed
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getShift() {
        return $this->shift;
    }
}