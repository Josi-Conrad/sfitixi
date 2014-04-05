<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:34
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedMonthlyDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="monthly")
 */
class RepeatedMonthlyDrivingAssertion extends RepeatedDrivingAssertion{
    /**
     * @ORM\Column(type="string", length=9)
     */
    protected $weekdayAsText;
    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $relativeWeekAsText;
    /**
     * @ORM\Column(type="integer")
     */
    protected $month;

    public function matching(Shift $shift)
    {
        // TODO: Implement matching() method.
    }
}