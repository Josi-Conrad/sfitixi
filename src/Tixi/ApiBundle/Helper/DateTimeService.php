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
 * This Class helps with time-calculations
 * Class DateTimeService
 * @package Tixi\ApiBundle\Helper
 */
class DateTimeService extends ContainerAware {
    /**
     * @return \DateTime
     */
    public static function getUTCnow() {
        return new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @return \DateTime
     */
    public static function getMaxDateTime() {
        return new \DateTime('2999-01-01');
    }

    /**
     * @param \DateTime $dateTime
     * @return float
     */
    public static function getMinutesOfDay(\DateTime $dateTime) {
        $midnight = clone $dateTime;
        $midnight->setTime(0, 0);
        $diff = $midnight->diff($dateTime);
        return ($diff->h * 60) + $diff->i;
    }

    /**
     * gets minute of day and checks if given time is between start and end
     * @param \DateTime $compareTime
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @return bool
     */
    public static function matchTimeBetweenTwoDateTimes(\DateTime $compareTime, \DateTime $startTime, \DateTime $endTime) {
        $start = self::getMinutesOfDay($startTime);
        $end = self::getMinutesOfDay($endTime);
        $compare = self::getMinutesOfDay($compareTime);

        if ($end > $start) {
            if ($compare >= $start && $compare <= $end) {
                return true;
            }
        } else {
            //if endminute is smaller then start, the time lays between midnight
            //so check if compare is not outside this time over midnight
            if (($compare >= $start && $compare >= $end)
            || ($compare <= $start && $compare <= $end)) {
                return true;
            }
        }
        return false;
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
     * @param \DateTime $dateTime
     * @return string
     * SO-8601 numeric representation of the day of the week [1 (for Monday) through 7 (for Sunday)]
     */
    public static function getWeekday(\DateTime $dateTime) {
        return $dateTime->format('N');
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