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

/**
 * Class DrivingAssertionPlan
 * @package Tixi\CoreDomain\Dispo
 *
 * @ORM\Entity
 * @ORM\Table(name="repeated_driving_assertion_plan")
 */
class RepeatedDrivingAssertionPlan {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $anchorDate;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endingDate;
    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingAssertion")
     * @ORM\JoinColumn(name="repeated_driving_assertion", referencedColumnName="id")
     */
    protected $repeatedDrivingAssertions;

    protected function __construct() {
        $this->repeatedDrivingAssertions = new ArrayCollection();
    }

    public static function registerRepeatedAssertionPlan(\DateTime $anschorDate, \DateTime $endingDate=null) {
        $assertion = new RepeatedDrivingAssertionPlan();
        $assertion->setAnchorDate($anschorDate);
        $assertion->setEndingDate($endingDate);

        return $assertion;
    }

    public function replaceDrivingAssertions(array $assertions) {
        $this->repeatedDrivingAssertions->clear();
        foreach($assertions as $assertion) {
            $this->repeatedDrivingAssertions->add($assertion);
        }
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
     * @param mixed $repeatedDrivingAssertions
     */
    public function setRepeatedDrivingAssertions($repeatedDrivingAssertions)
    {
        $this->repeatedDrivingAssertions = $repeatedDrivingAssertions;
    }

    /**
     * @return mixed
     */
    public function getRepeatedDrivingAssertions()
    {
        return $this->repeatedDrivingAssertions;
    }





} 