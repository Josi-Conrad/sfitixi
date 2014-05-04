<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:32
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Dispo\WorkingDay;

/**
 * Class WorkingMonthDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class WorkingMonthNewDTO {

    public $workingMonthDateYear;
    public $workingMonthDateMonth;
    public $workingMonthMemo;

}