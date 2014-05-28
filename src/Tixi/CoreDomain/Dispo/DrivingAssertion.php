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
class DrivingAssertion extends CommonBaseEntity implements DrivingAssertionInterface{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
     /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Driver", inversedBy="drivingAssertions")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    protected $driver;
    /**
     * @ORM\ManyToOne(targetEntity="Shift", inversedBy="drivingAssertions")
     * @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     */
    protected $shift;
    /**
     * @ORM\OneToOne(targetEntity="DrivingPool", mappedBy="drivingAssertion")
     */
    protected $drivingPool;
    /**
     * @param Shift $shift
     * @return mixed
     */
    public function matching(Shift $shift)
    {
        return $shift->getId() === $this->shift->getId();
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

    public function assignDriver(Driver $driver) {
        $this->driver = $driver;
        $driver->assignDrivingAssertion($this);
    }

    public function assignShift(Shift $shift) {
        $this->shift = $shift;
        $shift->assignDrivingAssertion($this);
    }

    public function assignDrivingPool(DrivingPool $drivingPool) {
        $this->drivingPool = $drivingPool;
        $drivingPool->assignDrivingAssertion($this);
    }

    public function isAssignedToDrivingPool() {
        return (isset($this->drivingPool) && null!==$this->drivingPool);
    }

    /**
     * @return mixed
     */
    public function getDriver()
    {
        return $this->driver;
    }


}