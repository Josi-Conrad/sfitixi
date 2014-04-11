<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 17:46
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;
use Tixi\CoreDomain\Shared\Entity;

/**
 * Class DrivingAssertionPlan
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionPlanRepositoryDoctrine")
 * @ORM\Table(name="repeated_driving_assertion_plan")
 */
class RepeatedDrivingAssertionPlan extends CommonBaseEntity implements Entity{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="text")
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

    protected function __construct() {
        $this->repeatedDrivingAssertions = new ArrayCollection();
        parent::__construct();
    }

    public static function registerRepeatedAssertionPlan($memo, \DateTime $anchorDate, $frequency, $withHoldidays, \DateTime $endingDate=null) {
        $assertion = new RepeatedDrivingAssertionPlan();
        $assertion->setMemo($memo);
        $assertion->setAnchorDate($anchorDate);
        $assertion->setEndingDate($endingDate);
        $assertion->setFrequency($frequency);
        $assertion->setWithHolidays($withHoldidays);
        return $assertion;
    }

    public function replaceRepeatedDrivingAssertions(ArrayCollection $assertions) {
        $this->repeatedDrivingAssertions->clear();
        foreach($assertions as $assertion) {
            $this->repeatedDrivingAssertions->add($assertion);
        }
    }

    public function assignDriver(Driver $driver) {
        $this->driver = $driver;
    }

    public function removeDriver() {
        $this->driver = null;
    }

    /**
     * @param mixed $anchorDate
     */
    public function setAnchorDate($anchorDate)
    {
        $this->anchorDate = $anchorDate;
    }

    /**
     * @return mixed
     */
    public function getAnchorDate()
    {
        return $this->anchorDate;
    }

    /**
     * @param mixed $endingDate
     */
    public function setEndingDate($endingDate)
    {
        $this->endingDate = $endingDate;
    }

    /**
     * @return mixed
     */
    public function getEndingDate()
    {
        return $this->endingDate;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo()
    {
        return $this->memo;
    }



    /**
     * @return mixed
     */
    public function getRepeatedDrivingAssertions()
    {
        return $this->repeatedDrivingAssertions;
    }

    /**
     * @param mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @return mixed
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param mixed $withHolidays
     */
    public function setWithHolidays($withHolidays)
    {
        $this->withHolidays = $withHolidays;
    }

    /**
     * @return mixed
     */
    public function getWithHolidays()
    {
        return $this->withHolidays;
    }







} 