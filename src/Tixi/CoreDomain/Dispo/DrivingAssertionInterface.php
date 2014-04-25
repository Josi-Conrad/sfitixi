<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:07
 */

namespace Tixi\CoreDomain\Dispo;

/**
 * Interface DrivingAssertionInterface
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingAssertionInterface {
    /**
     * @param Shift $shift
     * @return mixed
     */
    public function matching(Shift $shift);
} 