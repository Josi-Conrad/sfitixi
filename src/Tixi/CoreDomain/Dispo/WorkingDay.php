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
use Tixi\ApiBundle\Helper\WeekdayService;

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
     * @ORM\ManyToOne(targetEntity="WorkingMonth", inversedBy="workingDays")
     * @ORM\JoinColumn(name="working_month_id", referencedColumnName="id")
     */
    protected $workingMonth;
    /**
     * @ORM\Column(type="date")
     */
    protected $date;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;
    /**
     * total amount of driven distance on this day in meters
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $totalDistanceOfOrders;
    /**
     * total amount of driven time on this day in minutes
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $totalTimeOfOrders;

    protected function __construct() {
        $this->shifts = new ArrayCollection();
    }

    /**
     * Create workingDay - use \DateTime('today') to create Date with Time 00:00:00
     * @param \DateTime $date
     * @return WorkingDay
     */
    public static function registerWorkingDay(\DateTime $date) {
        $workingDay = new WorkingDay();
        $workingDay->setDate($date);
        return $workingDay;
    }

    /**
     * @param WorkingMonth $workingMonth
     */
    public function assignWorkingMonth(WorkingMonth $workingMonth) {
        $this->workingMonth = $workingMonth;
    }

    /**
     * @param Shift $shift
     */
    public function assignShift(Shift $shift) {
        $this->shifts->add($shift);
    }

    public function getDateString() {
        return $this->getDate()->format('d.m.Y');
    }

    public function getWeekDayAsString() {
        return WeekdayService::$numericToWeekdayConverter[$this->getDate()->format('N')] . '.name';
    }

    public function getShiftsOrderedByStartTime() {
        $orderedShifts = $this->getShifts()->toArray();
        usort($orderedShifts, function (Shift $a, Shift $b) {
            if ($a->getStart() < $b->getStart()) {
                return -1;
            } else if ($a->getStart() == $b->getStart()) {
                return 0;
            } else {
                return 1;
            }
        });
        return $orderedShifts;
    }

    /**
     * @return array
     */
    public function getMisingDriversInformationArray() {
        $missingDriversArray = array('perShiftString' => '', 'total' => 0);
        $shifts = $this->getShiftsOrderedByStartTime();
        $total = 0;
        /** @var Shift $shift */
        foreach ($shifts as $shift) {
            $missingDrivers = $shift->getAmountOfMissingDrivers();
            $correctedMissingDrivers = $missingDrivers < 0 ? 0 : $missingDrivers;
            $assignedPositions = count($shift->getDrivingAssertionsAsArray());
            $missingDriversArray['perShiftString'] .= $shift->getShiftType()->getName()
                . ': ' . $assignedPositions . '/' . $shift->getAmountOfDrivers() . ' ';
            $total += $correctedMissingDrivers;
        }
        $missingDriversArray['total'] = $total;
        return $missingDriversArray;
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

    public function getWorkingMonth() {
        return $this->workingMonth;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set DateTime with Time 00:00:00
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date) {
        $this->date = $date->setTime(0, 0);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Shift[]
     */
    public function getShifts() {
        return $this->shifts;
    }

    public function getShiftsAsArray() {
        return $this->shifts->toArray();
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

}