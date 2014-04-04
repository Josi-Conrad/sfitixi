<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:33
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="weekly")
 */
class RepeatedWeeklyDrivingAssertion extends RepeatedDrivingAssertion{
    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;
    /**
     * @ORM\Column(type="integer")
     */
    protected $week;

    public function matching(Shift $shift)
    {
        // TODO: Implement matching() method.
    }
}