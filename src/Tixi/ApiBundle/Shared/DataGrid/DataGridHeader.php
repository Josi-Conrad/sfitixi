<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 22:27
 */

namespace Tixi\ApiBundle\Shared\DataGrid;;

/**
 * Class DataGridHeader
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
class DataGridHeader {
    private $fieldPropertyName;
    private $headerName;
    private $order;

    /**
     * @param $fieldPropertyName
     * @param $headerName
     * @param $order
     */
    public function __construct($fieldPropertyName, $headerName, $order) {
        $this->fieldPropertyName = $fieldPropertyName;
        $this->headerName = $headerName;
        $this->order = $order;
    }

    /**
     * @param DataGridHeader $a
     * @param DataGridHeader $b
     * @return mixed
     */
    public static function compare(DataGridHeader $a, DataGridHeader $b) {
        return $a->order - $b->order;
    }

    /**
     * @return mixed
     */
    public function getHeaderName()
    {
        return $this->headerName;
    }

    /**
     * @return mixed
     */
    public function getFieldPropertyName()
    {
        return $this->fieldPropertyName;
    }


} 