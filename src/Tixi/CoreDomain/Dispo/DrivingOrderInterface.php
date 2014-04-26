<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:57
 */

namespace Tixi\CoreDomain\Dispo;

/**
 * Interface DrivingOrderInterface
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingOrderInterface {
    /**
     * This matches a DateTime and returns true if the Order is on that Date
     * @param \DateTime $date
     * @return mixed
     */
    public function matching(\DateTime $date);
} 