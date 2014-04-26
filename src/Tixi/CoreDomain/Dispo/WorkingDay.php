<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;

/**
 * Tixi\CoreDomain\Dispo\WorkingDay
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\WorkingDayRepositoryDoctrine")
 * @ORM\Table(name="working_day")
 */
class WorkingDay {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * ShiftPerDay (amount of all shifttypes)
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="workingDay")
     * @ORM\JoinColumn(name="shift_id", referencedColumnName="id")
     */
    protected $shifts;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;


    protected function __construct() {
        $shiftTypes = array();
        foreach ($shiftTypes as $shiftType) {
            $this->shifts[$shiftType] = new Shift($shiftType);
        }

        $this->shifts = new ArrayCollection();
        $this->drivingPools = new ArrayCollection();
    }

    /**
     * @param ShiftType $shiftTyp
     * @param Driver $driver
     */
    protected function assignDriver(ShiftType $shiftTyp, Driver $driver) {
        $this->shifts[$shiftTyp]->assignDriver($driver);
    }

    /**
     * @param DrivingMission $mission
     */
    protected function getPossibleDrivingPoolForMission(DrivingMission $mission) {
        $responsibleShift = null;
        foreach ($this->shifts as $shift) {
            if ($shift->isResponsibleForTime($shift)) {
                $responsibleShift = $shift;
            }
        }

    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getShifts()
    {
        return $this->shifts;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $drivingPools
     */
    public function setDrivingPools($drivingPools) {
        $this->drivingPools = $drivingPools;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDrivingPools() {
        return $this->drivingPools;
    }


}