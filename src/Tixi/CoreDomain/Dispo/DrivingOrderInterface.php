<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.04.14
 * Time: 15:57
 */

namespace Tixi\CoreDomain\Dispo;


interface DrivingOrderInterface {
    public function matching(\DateTime $date);
} 