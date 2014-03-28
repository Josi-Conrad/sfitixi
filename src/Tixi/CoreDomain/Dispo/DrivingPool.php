<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Vehicle;

class DrivingPool {

    protected $id;
    protected $driver;
    protected $vehicle;
    protected $correspondingShift;



    public function __construct(Driver $driver, Shift $shift) {
        $this->driver = $driver;
        $this->correspondingShift = $shift;
    }

    public function isCompleted() {
        return (isset($this->vehicle));
    }

    public function assignVehicle(Vehicle $vehicle) {
        $this->vehicle = $vehicle;
    }

    public function checkCompabilityForDrivingMission(DrivingMission $drivingMission) {

    }

}