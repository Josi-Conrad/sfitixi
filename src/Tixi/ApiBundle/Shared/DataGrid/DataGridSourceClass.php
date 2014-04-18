<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 21:47
 */

namespace Tixi\ApiBundle\Shared\DataGrid;

/**
 * Interface DataGridSourceClass
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
interface DataGridSourceClass {
    /**
     * @return mixed
     */
    public function getAccessQuery();

} 