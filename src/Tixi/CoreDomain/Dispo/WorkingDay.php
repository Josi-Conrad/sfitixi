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