<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 07.05.14
 * Time: 11:45
 */

namespace Tixi\CoreDomain\Dispo;


interface DrivingOrderRepositoryInterface {

    public function findAllOrdersForShift(Shift $shift);

} 