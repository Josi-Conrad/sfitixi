<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:06
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RepeatedDrivingAssertionRepositoryDoctrine")
 * @ORM\Table(name="repeated_driving_assertion")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"daily" = "RepeatedDailyDrivingAssertion",
 * "weekly" = "RepeatedWeeklyDrivingAssertion", "monthly" = "RepeatedMonthlyDrivingAssertion"})
 */
abstract class RepeatedDrivingAssertion implements DrivingAssertionInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="RepeatedDrivingAssertionPlan", inversedBy="repeatedDrivingAssertions")
     * @ORM\JoinColumn(name="repeated_assertion_plan_id", referencedColumnName="id")
     */
    protected $assertionPlan;
    /**
     * @ORM\ManyToMany(targetEntity="ShiftType")
     * @ORM\JoinTable(name="repeateddrivingassertion_to_shifttypes",
     *      joinColumns={@ORM\JoinColumn(name="repeateddrivingassertion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="shifttype_id", referencedColumnName="id")})
     */
    protected $shiftTypes;

    public function __construct() {
        $this->shiftTypes = new ArrayCollection();
    }

    public abstract function matching(Shift $shift);

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
     * @param ShiftType $shiftType
     */
    public function addShiftType(ShiftType $shiftType)
    {
        $this->shiftTypes->add($shiftType);
    }

    /**
     * @param mixed $shiftTypes
     */
    public function setShiftTypes($shiftTypes)
    {
        $this->shiftTypes = $shiftTypes;
    }

    /**
     * @return mixed
     */
    public function getShiftTypes()
    {
        return $this->shiftTypes;
    }


}