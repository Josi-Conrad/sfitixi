<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 21:56
 */

namespace Tixi\CoreDomainBundle\Util;


class GeometryService {

    //defines the number of digits after the decimal point
    const PRECISION_DECIMAL = 7;
    //defines the multiplication factor for serialize as int
    const PRECISION_FACTOR = 10000000;

    public static function serialize($coordinateFloat) {
        $coordinateFloat = round($coordinateFloat,self::PRECISION_DECIMAL);
        $coordinateBigInt = $coordinateFloat*self::PRECISION_FACTOR;
        return $coordinateBigInt;
    }

    public static function deserialize($coordinateBigInt) {
        $cooredinateFloat = $coordinateBigInt/self::PRECISION_FACTOR;
        $cooredinateFloat = round($cooredinateFloat, self::PRECISION_DECIMAL);
        return $cooredinateFloat;
    }
} 