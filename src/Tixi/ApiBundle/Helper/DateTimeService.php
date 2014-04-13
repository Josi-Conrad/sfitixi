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
            return $this->convertToLocalDateTime($utcDate)->format('d.m.Y H:i');
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