<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:37
 */

namespace Tixi\App\Driving;


use Tixi\CoreDomain\Address;

interface DrivingOrderService {

    /**
     * @param \DateTime $date
     * @return DrivingOrder[]
     */
    public function getAllDrivingOrdersForDate(\DateTime $date);

    /**
     * @return ShiftType[]
     */
    public function getAvailableShiftTypes();

    public function getRouteForDrivingOrder();

    public function getZoneForAddress(Address $addressTo);

}