<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Driver;

class Shift {

    protected $id;
    /**
     * @var ShiftType
     */
    protected $shiftType;
    protected $amountOfDrivers;
    /**
     * @var array
     */
    protected $drivingPools;

    protected function __construt(ShiftType $shiftType) {
        $this->shiftType = $shiftType;
    }


    protected function assignDriver(Driver $driver) {
        $this->drivingPools = new DrivingPool($driver, $this);
    }

    protected function amountOfDriversNeede() {
        return $this->amountOfDrivers-$this->count($this->drivingPools);
    }



}

//    public function getAmountOfDrivers() {
//        return count($this->drivingPools);
//    }

