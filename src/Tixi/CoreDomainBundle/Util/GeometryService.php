<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 21:56
 */

namespace Tixi\CoreDomainBundle\Util;

/**
 * Since we use integer in database for compatibility, this service recalculate geolocation coordinates
 * from floating point to integer and vice versa.
 * A precision of 7 is given, a minimum of 6 is required for good OSRM results
 * Class GeometryService
 * @package Tixi\CoreDomainBundle\Util
 */
class GeometryService {

    //defines the number of digits after the decimal point
    const PRECISION_DECIMAL = 7;
    //defines the multiplication factor for serialize as int
    const PRECISION_FACTOR = 10000000;

    /**
     * converts floating point to integer with precision
     * @param $coordinateFloat
     * @return float
     */
    public static function serialize($coordinateFloat) {
        $coordinateFloat = round($coordinateFloat,self::PRECISION_DECIMAL);
        $coordinateBigInt = $coordinateFloat*self::PRECISION_FACTOR;
        return $coordinateBigInt;
    }

    /**
     * converts integer to floating point with precision
     * @param $coordinateBigInt
     * @return float
     */
    public static function deserialize($coordinateBigInt) {
        $cooredinateFloat = $coordinateBigInt/self::PRECISION_FACTOR;
        $cooredinateFloat = round($cooredinateFloat, self::PRECISION_DECIMAL);
        return $cooredinateFloat;
    }
} 