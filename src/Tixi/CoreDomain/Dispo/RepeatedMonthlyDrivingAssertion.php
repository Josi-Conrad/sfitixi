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
     * @ORM\Column(type="integer")
     */
    protected $weekday;
    /**
     * @ORM\Column(type="integer")
     */
    protected $week;
    /**
     * @ORM\Column(type="integer")
     */
    protected $month;

    public function matching(Shift $shift)
    {
        // TODO: Implement matching() method.
    }
}