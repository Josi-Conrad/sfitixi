<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;


class ShiftType {

    protected $start;
    protected $end;

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isResponsibleForTime(\DateTime $dateTime) {
        //TodDo
        return true;
    }

} 