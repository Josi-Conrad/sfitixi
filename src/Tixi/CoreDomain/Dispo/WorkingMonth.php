<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\WorkingMonth
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\WorkingMonthRepositoryDoctrine")
 * @ORM\Table(name="working_month")
 */
class WorkingMonth extends CommonBaseEntity {
    /** status of workingMonth (plan) */
    const OPEN = 0;
    const SENT = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * amount of all days per Month
     * @ORM\OneToMany(targetEntity="WorkingDay", mappedBy="workingMonth")
     * @ORM\JoinColumn(name="working_day_id", referencedColumnName="id")
     */
    protected $workingDays;
    /**
     * @ORM\Column(type="date")
     * @var $date \DateTime
     */
    protected $date;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;
    /**
     * Represents a status number, like 1=email sent, 2=freezed...
     * @ORM\Column(type="integer")
     */
    protected $status;

    protected function __construct() {
        $this->workingDays = new ArrayCollection();
        parent::__construct();
    }

    /**
     * $date could be \DateTime->format('first day of this month') on a specific month
     * @param \DateTime $date
     * @param int $status
     * @return WorkingMonth
     */
    public static function registerWorkingMonth(\DateTime $date, $status = self::OPEN) {
        $workingMonth = new WorkingMonth();
        $date->modify('first day of this month');
        $workingMonth->setDate($date);
        $workingMonth->setStatus($status);
        return $workingMonth;
    }

    /**
     *TimePeriod from start day of month to next start day of month
     *(amount of days = amount of days in this month)
     */
    public function createWorkingDaysForThisMonth() {
        /**@var $start \DateTime */
        $start = $this->date;
        $start->modify('first day of this month');

        $end = clone $start;
        $end->modify('first day of next month');

        //interval per day
        $interval = new \DateInterval('P1D');
        $days = new \DatePeriod($start, $interval, $end);

        foreach ($days as $day) {
            $workingDay = WorkingDay::registerWorkingDay($day);
            $this->assignWorkingDay($workingDay);
            $workingDay->assignWorkingMonth($this);
        }
    }

    public function assignWorkingDay(WorkingDay $workingDay) {
        $this->workingDays->add($workingDay);
    }

    /**
     * @param mixed $date
     */
    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $workingDays
     */
    public function setWorkingDays($workingDays) {
        $this->workingDays = $workingDays;
    }

    /**
     * @return WorkingDay[]
     */
    public function getWorkingDays() {
        return $this->workingDays;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
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