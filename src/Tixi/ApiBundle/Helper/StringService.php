<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 14:18
 */

namespace Tixi\ApiBundle\Helper;


class StringService {

    public static function convertStringToASCII($string) {
        return strtr(utf8_decode($string),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
    }
} 