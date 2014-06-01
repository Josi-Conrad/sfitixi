<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 17:46
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Class DrivingAssertionPlan
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionPlanRepositoryDoctrine")
 * @ORM\Table(name="repeated_driving_assertion_plan")
 */
class RepeatedDrivingAssertionPlan extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $subject;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;
    /**
     * @ORM\Column(type="date")
     */
    protected $anchorDate;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $endingDate;
    /**
     * @ORM\Column(type="string", length=7)
     */
    protected $frequency;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $withHolidays;
    /**
     * @ORM\OneToMany(targetEntity="RepeatedDrivingAssertion", mappedBy="assertionPlan")
     */
    protected $repeatedDrivingAssertions;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Driver", inversedBy="repeatedDrivingAssertionPlans")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    protected $driver;
    /**
     * @ORM\OneToMany(targetEntity="DrivingAssertion", mappedBy="repeatedDrivingAssertionPlan")
     */
    protected $drivingAssertions;

    protected function __construct() {
        $this->repeatedDrivingAssertions = new ArrayCollection();
        $this->drivingAssertions = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param string $subject
     * @param \DateTime $anchorDate
     * @param string $frequency
     * @param boolean $withHolidays
     * @param \DateTime $endingDate
     * @param string $memo
     * @return RepeatedDrivingAssertionPlan
     */
    public static function registerRepeatedAssertionPlan($subject, \DateTime $anchorDate, $frequency, $withHolidays, \DateTime $endingDate = null, $memo = null) {
        $endingDate = (null !== $endingDate) ? $endingDate : DateTimeService::getMaxDateTime();
        $assertion = new RepeatedDrivingAssertionPlan();
        $assertion->setSubject($subject);
        $assertion->setMemo($memo);
        $assertion->setAnchorDate($anchorDate);
        $assertion->setEndingDate($endingDate);
        $assertion->setFrequency($frequency);
        $assertion->setWithHolidays($withHolidays);
        return $assertion;
    }

    /**
     * @param ArrayCollection $assertions
     */
    public function replaceRepeatedDrivingAssertions(ArrayCollection $assertions) {
        $this->repeatedDrivingAssertions->clear();
        foreach ($assertions as $assertion) {
            $this->repeatedDrivingAssertions->add($assertion);
        }
    }

    /**
     * @param RepeatedDrivingAssertion $repeatedDrivingAssertion
     */
    public function assignRepeatedDrivingAssertion(RepeatedDrivingAssertion $repeatedDrivingAssertion) {
        $this->getRepeatedDrivingAssertions()->add($repeatedDrivingAssertion);
    }

    public function assigneDrivingAssertion(DrivingAssertion $drivingAssertion) {
        $this->drivingAssertions->add($drivingAssertion);
    }

    public function removeDrivingAssertion(DrivingAssertion $drivingAssertion) {
        $this->drivingAssertions->removeElement($drivingAssertion);
    }

    /**
     * @param Driver $driver
     */
    public function assignDriver(Driver $driver) {
        $this->driver = $driver;
    }

    public function removeDriver() {
        $this->driver = null;
    }

    /**
     * @return Driver
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @param mixed $anchorDate
     */
    public function setAnchorDate($anchorDate) {
        $this->anchorDate = $anchorDate;
    }

    /**
     * @return mixed
     */
    public function getAnchorDate() {
        return $this->anchorDate;
    }

    /**
     * @param mixed $endingDate
     */
    public function setEndingDate($endingDate) {
        $this->endingDate = $endingDate;
    }

    /**
     * @return mixed
     */
    public function getEndingDate() {
        return $this->endingDate;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
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
     * @return ArrayCollection
     */
    public function getRepeatedDrivingAssertions() {
        return $this->repeatedDrivingAssertions;
    }

    public function getRepeatedDrivingAssertionsAsArray() {
        return $this->repeatedDrivingAssertions->toArray();
    }

    /**
     * @param mixed $frequency
     */
    public function setFrequency($frequency) {
        $this->frequency = $frequency;
    }

    /**
     * @return mixed
     */
    public function getFrequency() {
        return $this->frequency;
    }

    /**
     * @param mixed $withHolidays
     */
    public function setWithHolidays($withHolidays) {
        $this->withHolidays = $withHolidays;
    }

    /**
     * @return boolean
     */
    public function getWithHolidays() {
        return $this->withHolidays;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
    }

}