<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Driver;

class WorkingDay {


    /**
     * @var array
     * ShiftPerDay
     */
    protected $shifts;

    /**
     * @var DateTime
     */
    protected $date;

    protected function __construct() {
        $shiftTypes = array();
        foreach($shiftTypes as $shiftType) {
            $this->shifts[$shiftType] = new Shift($shiftType);
        }
    }

    protected function assignDriver(ShiftType $shiftTyp, Driver $driver) {
        $this->shifts[$shiftTyp]->assignDriver($driver);
    }

    protected function getPossibleDrivingPoolForMission(DrivingMission $mission) {
        $responsibleShift = null;
        foreach($this->shifts as $shift) {
            if($shift->isResponsibleForTime($shift)) {
                $responsibleShift = $shift;
            }
        }

    }






} 