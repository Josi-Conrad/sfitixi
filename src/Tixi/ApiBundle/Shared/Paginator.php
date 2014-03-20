<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 10:01
 */

namespace Tixi\ApiBundle\Shared;


class Paginator {

    public static function adjustPageForPagination($page) {
        return (is_null($page) || $page < 0) ? 0 : $page - 1;
    }
} 