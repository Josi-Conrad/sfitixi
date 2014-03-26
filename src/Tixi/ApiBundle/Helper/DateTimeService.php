<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 21.03.14
 * Time: 15:10
 */

namespace Tixi\ApiBundle\Helper;


use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Validator\Constraints\DateTime;

class DateTimeService extends ContainerAware {
    /**
     * @param \DateTime $utcDate
     * @return string
     */
    public function convertUTCDateTimeToLocalString($utcDate) {
        if (!empty($utcDate)) {
            return $this->convertUTCDateTimeToLocalDateTime($utcDate)->format('d.m.Y');
        }
    }

    /**
     * @param \DateTime $utcDate
     * @return \DateTime
     */
    public function convertUTCDateTimeToLocalDateTime($utcDate) {
        if (!empty($utcDate)) {
            $localDate = clone $utcDate;
            $localDate->setTimeZone(new \DateTimeZone($this->container->getParameter('time_zone')));
            return $localDate;
        }
    }

    /**
     * @param $localDateStr
     * @return \DateTime
     */
    public function convertLocalDateTimeStringToUTCDateTime($localDateStr) {
        if (!empty($localDateStr)) {
            $localDate = \DateTime::createFromFormat('d.m.Y', $localDateStr);
            if ($localDate) {
                $this->convertLocalDateTimeToUTCDateTime($localDate);
            }
            return $localDate;
        }
    }

    /**
     * @param \DateTime $localDate
     * @return \DateTime
     */
    public function convertLocalDateTimeToUTCDateTime($localDate) {
        if (!empty($localDate)) {
            $utcDate = clone $localDate;
            $utcDate->setTimezone(new \DateTimeZone('UTC'));
            return $utcDate;
        }
    }

} 