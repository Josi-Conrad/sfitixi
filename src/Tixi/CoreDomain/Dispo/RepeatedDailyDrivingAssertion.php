<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:35
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\RepeatedDailyDrivingAssertion
 *
 * @ORM\Entity
 * @ORM\Table(name="daily")
 */
class RepeatedDailyDrivingAssertion extends RepeatedDrivingAssertion {

    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    protected $shifts;

    public function matching(Shift $shift) {
        // TODO: Implement matching() method.
    }
}