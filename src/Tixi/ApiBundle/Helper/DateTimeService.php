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

/**
 * Class DateTimeService
 * @package Tixi\ApiBundle\Helper
 */
class DateTimeService extends ContainerAware {

    public static function getUTCnow() {
        return new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public static function getMaxDateTime() {
        return new \DateTime('2999-01-01');
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     * returns four digit year
     */
    public static function getYear(\DateTime $dateTime) {
        return $dateTime->format('Y');
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     * returns two digit month number (01-12)
     */
    public static function getMonth(\DateTime $dateTime) {
        return $dateTime->format('m');
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     * returns two digit day of month (01-31)
     */
    public static function getDayOfMonth(\DateTime $dateTime) {
        return $dateTime->format('d');
    }

    /**
     * @param \DateTime $utcDate
     * @return \DateTime
     */
    public function convertToLocalDateTime($utcDate) {
        if (null !== $utcDate) {
            $localDate = clone $utcDate;
            $localDate->setTimeZone(new \DateTimeZone($this->container->getParameter('time_zone')));
            return $localDate;
        }
    }

    /**
     * Not used anymore to persist,
     * as doctrine saves all DateTimes to UTC (utcdatetime type)
     * @param \DateTime $localDate
     * @return \DateTime
     */
    public function convertToUTCDateTime($localDate) {
        if (null !== $localDate) {
            $utcDate = clone $localDate;
            $utcDate->setTimezone(new \DateTimeZone('UTC'));
            return $utcDate;
        }
    }

    /**
     * @param \DateTime $utcDate
     * @return string
     */
    public function convertToLocalDateTimeString($utcDate) {
        if (null !== $utcDate) {
            $localDateTime = $this->convertToLocalDateTime($utcDate);
            return $this->convertDateTimeToDateTimeString($localDateTime);
        }
    }

    public function convertDateTimeToDateTimeString(\DateTime $dateTime) {
        $formatedDate = $dateTime->format('d.m.Y');
        $formatedTime = $dateTime->format('H:i');
        return $formatedDate . ' - ' . $formatedTime;
    }

    /**
     * @param \DateTime $utcDate
     * @return string
     */
    public function convertToLocalTimeString($utcDate) {
        if (null !== $utcDate) {
            return $this->convertToLocalDateTime($utcDate)->format('H:i');
        }
    }
    /**
     * @param $localDateStr
     * @return \DateTime
     */
    public function convertDateTimeStringToUTCDateTime($localDateStr) {
        if (null !== $localDateStr) {
            $localDate = \DateTime::createFromFormat('d.m.Y H:i', $localDateStr);
            if ($localDate) {
                $this->convertToUTCDateTime($localDate);
            }
            return $localDate;
        }
    }
}