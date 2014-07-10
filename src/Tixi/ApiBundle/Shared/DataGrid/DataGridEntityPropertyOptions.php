<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 30.06.14
 * Time: 11:36
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


class DataGridEntityPropertyOptions {
    const DEFAULTSORT_ASC = 'ASC';
    const DEFDAULTSORT_DESC = 'DESC';

    public $comparingOperator;
    public $isRestrictive;
    public $isHeader;
    public $defaultSort;
    public $isComputed;
}