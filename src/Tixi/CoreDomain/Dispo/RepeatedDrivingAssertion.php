<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:06
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="repeated_driving_assertion")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"daily" = "DailyRepeatedDrivingAssertion",
 * "weekly" = "WeeklyRepeatedDrivingAssertion", "monthly" = "MonthlyRepeatedDrivingAssertion"})
 */
abstract class RepeatedDrivingAssertion implements DrivingAssertionInterface {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $anchorDate;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $endingDate;

    public abstract function matching(Shift $shift);
}