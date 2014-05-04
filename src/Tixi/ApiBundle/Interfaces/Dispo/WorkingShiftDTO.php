<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 18:32
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class WorkingShiftDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class WorkingShiftDTO {
    public $workingShiftId;
    public $workingShiftAmountOfDrivers;

    /**
     * @return mixed
     */
    public function getWorkingShiftAmountOfDrivers() {
        return $this->workingShiftAmountOfDrivers;
    }

    /**
     * @return mixed
     */
    public function getWorkingShiftId() {
        return $this->workingShiftId;
    }

}

