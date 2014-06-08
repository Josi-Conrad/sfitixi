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

    /**
     * creates and sends MonthPlan to all drivers with e-mail
     * @param \DateTime $date
     * @return mixed
     */
    public function sendMonthPlanToAllDrivers(\DateTime $date);
} 