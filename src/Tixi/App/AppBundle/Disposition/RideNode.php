<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 21:20
 */

namespace Tixi\App\AppBundle\Disposition;

use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\DrivingMission;

/**
 * Simple Node DTO to save relevant Information for routeConfiguration calculation
 * Class RideNode
 * @package Tixi\App\AppBundle\Disposition
 */
class RideNode {
    const RIDE_EMPTY = 0;
    const RIDE_PASSENGER = 1;

    /**
     * this type represents a passenger ride or an empty ride (between 2 missions)
     * @var int
     */
    public $type;

    /**
     * persist the mission too for possible configuration buildings with pools
     * @var DrivingMission
     */
    public $drivingMission;

    /**
     * @var int
     */
    public $startMinute;
    /**
     * @var int
     */
    public $endMinute;
    /**
     * @var Address
     */
    public $startAddress;
    /**
     * @var Address
     */
    public $endAddress;

    /**
     * @param $type
     */
    protected function __construct($type) {
        $this->type = $type;
    }

    public static function registerPassengerRide(DrivingMission $drivingMission, Address $startAddress, Address $endAddress) {
        $ride = new RideNode(self::RIDE_PASSENGER);
        $ride->drivingMission = $drivingMission;

        $ride->startMinute = $drivingMission->getServiceMinuteOfDay();
        $ride->endMinute = $drivingMission->getServiceMinuteOfDay() + $drivingMission->getServiceDuration();

        $ride->startAddress = $startAddress;
        $ride->endAddress = $endAddress;

        return $ride;
    }

    public static function registerEmptyRide(Address $startAddress, Address $endAddress) {
        $ride = new RideNode(self::RIDE_EMPTY);

        $ride->startAddress = $startAddress;
        $ride->endAddress = $endAddress;

        return $ride;
    }

    /**
     * @return int
     */
    public function getDuration() {
        return $this->endMinute - $this->startMinute;
    }
} 