<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Interfaces\Validators as Valid;
/**
 * Class AbsentRegisterDTO
 * @package Tixi\ApiBundle\Interfaces
 * @Valid\AbsentRegisterConstraint
 */
class AbsentRegisterDTO {
    public $id;
    public $subject;
    public $startDate;
    public $endDate;
    public $personId;

    public function __construct() {
        $this->startDate = new \DateTime('today');
        $this->endDate = new \DateTime('today');
    }
}

