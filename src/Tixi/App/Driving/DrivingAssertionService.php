<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 17:38
 */

namespace Tixi\App\Driving;


interface DrivingAssertionService {

    /**
     * @param \DateTime $date
     * @return Driver[]
     */
    public function getAllAvailableDriversForDate(\DateTime $date);
} 