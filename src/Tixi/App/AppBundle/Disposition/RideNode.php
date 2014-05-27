<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 21:20
 */

namespace Tixi\App\AppBundle\Disposition;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\App\Disposition\DispositionVariables;
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
    const RIDE_FEASIBLE = 2;

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
    public $targetAddress;
    /**
     * Duration in Minutes
     * @var int
     */
    public $duration;
    /**
     * @var int
     */
    public $distance;

    /**
     * @param $type
     */
    protected function __construct($type) {
        $this->type = $type;
    }

    /**
     * passenger ride, represents necessary details of a ordered ride
     * @param DrivingMission $drivingMission
     * @param Address $startAddress
     * @param Address $endAddress
     * @return RideNode
     */
    public static function registerPassengerRide(DrivingMission $drivingMission, Address $startAddress, Address $endAddress) {
        $ride = new RideNode(self::RIDE_PASSENGER);
        $ride->drivingMission = $drivingMission;

        $ride->duration = $drivingMission->getServiceDuration();
        $ride->distance = $drivingMission->getServiceDistance();
        $ride->startMinute = $drivingMission->getServiceMinuteOfDay();
        $ride->endMinute = $drivingMission->getServiceMinuteOfDay() + $drivingMission->getServiceDuration();

        $ride->startAddress = $startAddress;
        $ride->targetAddress = $endAddress;

        return $ride;
    }

    /**
     * empty ride which is possible between to rideNodes
     * @param Address $startAddress
     * @param Address $endAddress
     * @return RideNode
     */
    public static function registerEmptyRide(Address $startAddress, Address $endAddress) {
        $ride = new RideNode(self::RIDE_EMPTY);

        $ride->startAddress = $startAddress;
        $ride->targetAddress = $endAddress;

        $ride->startMinute = 0;
        $ride->endMinute = 0;

        return $ride;
    }

    /**
     * minimal Ride to check feasibility in a configuration
     * @param $direction
     * @param \DateTime $time
     * @param $duration //in Minutes
     * @param $additionalTime //from Passenger
     * @return RideNode
     */
    public static function registerFeasibleRide(\DateTime $time, $direction = DrivingMission::SAME_START, $duration, $additionalTime) {
        $ride = new RideNode(self::RIDE_FEASIBLE);

        if ($direction === DrivingMission::SAME_START) {
            $ride->startMinute = DateTimeService::getMinutesOfDay($time);
            $ride->duration = $duration + DispositionVariables::getBoardingTimes() + $additionalTime;
            $ride->endMinute = $ride->startMinute + $ride->duration;
        } else {
            $ride->endMinute = DateTimeService::getMinutesOfDay($time);
            $ride->duration = $duration + DispositionVariables::getBoardingTimes() + $additionalTime;
            $ride->startMinute = $ride->endMinute - $ride->duration;
        }

        return $ride;
    }

    /**
     * generates Hash from Start and Target Adress coordinates as string together, since
     * calculate sum from both bigInts would be the same ride from->to and to->from
     * @return string
     */
    public function getRideHash() {
        return hash('md2', $this->startAddress->getHashFromBigIntCoordinates()
            . $this->targetAddress->getHashFromBigIntCoordinates());
    }

    /**
     * @return int
     */
    public function getDuration() {
        return $this->endMinute - $this->startMinute;
    }
} 