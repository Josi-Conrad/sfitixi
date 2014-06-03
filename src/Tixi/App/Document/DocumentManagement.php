<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 03.06.14
 * Time: 18:55
 */

namespace Tixi\App\Document;

/**
 * Interface DocumentManagement
 * @package Tixi\App\Document
 */
interface DocumentManagement {
    /**
     * creates monthplan pdf and returns filepath
     * @param \DateTime $date
     * @return null|string
     */
    public function createMonthPlanDocument(\DateTime $date);

    public function sendMonthPlanToAllDrivers(\DateTime $date);
} 