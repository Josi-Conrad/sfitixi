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

/**
 * Tixi\CoreDomain\Dispo\Shift
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\ShiftRepositoryDoctrine")
 * @ORM\Table(name="shift")
 */
class Shift {
    /** status on the shift */
    const OPEN = 0;
    const FREEZED = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @var $shiftType ShiftType
     * @ORM\ManyToOne(targetEntity="ShiftType")
     * @ORM\JoinColumn(name="shift_type_id", referencedColumnName="id")
     */
    protected $shiftType;
    /**
     * @ORM\OneToMany(targetEntity="DrivingPool", mappedBy="shift")
     * @ORM\JoinColumn(name="driving_pool_id", referencedColumnName="id")
     */
    protected $drivingPools;
    /**
     * @var $workingDay WorkingDay
     * @ORM\ManyToOne(targetEntity="WorkingDay", inversedBy="shifts")
     * @ORM\JoinColumn(name="working_day_id", referencedColumnName="id")
     */
    protected $workingDay;
    /**
     * amount of drivers needed for day and shift (monthly planing)
     * @ORM\Column(type="integer")
     */
    protected $amountOfDrivers;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $manuallyEdited;

    protected function __construct() {
        $this->drivingPools = new ArrayCollection();
    }

    /**
     * Register new Shift from type ShiftType
     * @param WorkingDay $workingDay
     * @param ShiftType $shiftType
     * @param int $amountOfDrivers
     * @param int $status
     * @param bool $manuallyEdited
     * @return Shift
     */
    public static function registerShift(WorkingDay $workingDay, ShiftType $shiftType, $amountOfDrivers = 0,
                                         $status = self::OPEN, $manuallyEdited = false) {
        $shift = new Shift();
        $shift->setWorkingDay($workingDay);
        $shift->setShiftType($shiftType);
        $shift->setAmountOfDrivers($amountOfDrivers);
        $shift->setStatus($status);
        $shift->setManuallyEdited($manuallyEdited);
        return $shift;
    }

    /**
     * @return DrivingPool
     */
    public function getFirstDrivingPoolWithoutDriver() {
        /**@var $drivingPool DrivingPool */
        foreach ($this->getDrivingPools() as $drivingPool) {
            if (!$drivingPool->hasAssociatedDriver()) {
                return $drivingPool;
            }
        }
        return null;
    }

    /**
     * @return DrivingPool[]
     */
    public function getDrivingPoolsWithoutDriver() {
        $pools = array();
        /**@var $drivingPool DrivingPool */
        foreach ($this->getDrivingPools() as $drivingPool) {
            if (!$drivingPool->hasAssociatedDriver()) {
                array_push($pools, $drivingPool);
            }
        }
        return $pools;
    }

    /**
     * @param Driver $driver
     * @return bool
     */
    public function isDriverAssociatedToThisShift(Driver $driver) {
        foreach ($this->getDrivingPools() as $drivingPool) {
            $poolDriver = $drivingPool->getDriver();
            if ($poolDriver !== null) {
                if ($poolDriver->getId() === $driver->getId()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param Driver $driver
     */
    protected function assignDriver(Driver $driver) {
        $this->assignDrivingPool(DrivingPool::registerDrivingPool($this, $driver));
    }

    /**
     * @return mixed
     */
    protected function amountOfDriversNeeded() {
        return $this->amountOfDrivers - count($this->drivingPools);
    }

    /**
     * @param mixed $drivingPool
     */
    public function assignDrivingPool($drivingPool) {
        $this->drivingPools->add($drivingPool);
    }

    /**
     * @return ArrayCollection
     */
    public function getDrivingPools() {
        return $this->drivingPools;
    }

    /**
     * @return mixed
     */
    public function getAmountOfDrivers() {
        return $this->amountOfDrivers;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return ShiftType
     */
    public function getShiftType() {
        return $this->shiftType;
    }

    /**
     * @return WorkingDay
     */
    public function getWorkingDay() {
        return $this->workingDay;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->workingDay->getDate();
    }

    /**
     * returns startDate (workday) with Time (shiftType)
     */
    public function getStartDate() {
        $returnDate = clone $this->workingDay->getDate();
        return $returnDate->setTimestamp($this->getShiftType()->getStart()->getTimestamp());
    }

    /**
     * returns endDate (workDay) with Time (shiftType)
     */
    public function getEndDate() {
        $returnDate = clone $this->workingDay->getDate();
        return $returnDate->setTimestamp($this->getShiftType()->getEnd()->getTimestamp());
    }

    public function getStart(){
        return $this->shiftType->getStart();
    }

    public function getEnd(){
        return $this->shiftType->getEnd();
    }

    /**
     * @param mixed $amountOfDrivers
     */
    public function setAmountOfDrivers($amountOfDrivers) {
        $this->amountOfDrivers = $amountOfDrivers;
    }

    /**
     * @param mixed $drivingPool
     */
    public function setDrivingPool($drivingPool) {
        $this->drivingPool = $drivingPool;
    }

    /**
     * @param mixed $shiftType
     */
    public function setShiftType($shiftType) {
        $this->shiftType = $shiftType;
    }

    /**
     * @param \Tixi\CoreDomain\Dispo\WorkingDay $workingDay
     */
    public function setWorkingDay($workingDay) {
        $this->workingDay = $workingDay;
    }

    /**
     * @param mixed $manuallyEditet
     */
    public function setManuallyEdited($manuallyEditet) {
        $this->manuallyEdited = $manuallyEditet;
    }

    /**
     * @return mixed
     */
    public function getManuallyEdited() {
        return $this->manuallyEdited;
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