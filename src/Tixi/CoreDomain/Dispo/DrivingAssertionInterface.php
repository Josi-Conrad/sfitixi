<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:07
 */

namespace Tixi\CoreDomain\Dispo;


interface DrivingAssertionInterface {

    public function matching(Shift $shift);
} 