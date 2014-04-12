<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 11.04.14
 * Time: 15:39
 */

namespace Tixi\CoreDomainBundle\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Class UTCDateTimeTypeDoctrine
 * @package Tixi\CoreDomainBundle\Types
 */
class UTCDateTimeTypeDoctrine extends DateTimeType {
    static private $utc = null;

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if ($value === null) {
            return null;
        }
        $formatString = $platform->getDateTimeFormatString();

        $value->setTimezone((self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC')));

        $formatted = $value->format($formatString);

        return $formatted;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return \DateTime|mixed|null
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        if ($value === null) {
            return null;
        }

        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC'))
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
} 