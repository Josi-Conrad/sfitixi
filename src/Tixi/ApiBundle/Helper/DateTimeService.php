<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 21.03.14
 * Time: 15:10
 */

namespace Tixi\ApiBundle\Helper;


use Symfony\Component\DependencyInjection\ContainerAware;

class DateTimeService extends ContainerAware{

    public function convertUTCDateToLocalString(\DateTime $utcDate) {
        return $this->convertUTCDateTimeToLocalDateTime($utcDate)->format('d.m.Y');
    }

    public function convertUTCDateTimeToLocalDateTime(\DateTime $utcDate) {
        $localDate = clone $utcDate;
        $localDate->setTimeZone(new \DateTimeZone($this->container->getParameter('time_zone')));
        return $localDate;
    }

    public function convertLocalDateTimeToUTCDateTime(\DateTime $localDate) {
        $utcDate = clone $localDate;
        $utcDate->setTimezone(new \DateTimeZone('UTC'));
        return $utcDate;
    }

} 