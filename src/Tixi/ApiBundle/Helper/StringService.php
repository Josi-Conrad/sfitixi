<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 14:18
 */

namespace Tixi\ApiBundle\Helper;

/**
 * This Class helps with ASCI String-codes
 * Class StringService
 * @package Tixi\ApiBundle\Helper
 */
class StringService {

    /**
     * @param $string
     * @return string
     */
    public static function convertStringToASCII($string) {
        return strtr(utf8_decode($string),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
    }
} 