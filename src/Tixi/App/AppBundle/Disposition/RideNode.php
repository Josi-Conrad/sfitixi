<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 21:20
 */

namespace Tixi\App\AppBundle\Disposition;

use Tixi\CoreDomain\Address;

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
     * @param $startMinute
     * @param $endMinute
     * @param $startAddress
     * @param $endAddress
     */
    public function __construct($type, $startMinute, $endMinute, $startAddress, $endAddress) {
        $this->type = $type;
        $this->startMinute = $startMinute;
        $this->endMinute = $endMinute;
        $this->startAddress = $startAddress;
        $this->endAddress = $endAddress;
    }

    /**
     * @return int
     */
    public function getDuration() {
        return $this->endMinute - $this->startMinute;
    }
} 