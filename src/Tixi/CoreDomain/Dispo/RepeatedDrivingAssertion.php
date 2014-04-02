<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 16:06
 */

namespace Tixi\CoreDomain\Dispo;


abstract class RepeatedDrivingAssertion implements DrivingAssertionInterface {

    protected $anchorDate;
    protected $endingDate;

    public abstract function matching(Shift $shift);
}