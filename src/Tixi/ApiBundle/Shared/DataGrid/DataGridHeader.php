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
    protected $fieldPropertyName;
    protected $headerName;
    protected $order;
    protected $isComputed;

    /**
     * @param $fieldPropertyName
     * @param $headerName
     * @param $order
     * @param $isComputed
     */
    public function __construct($fieldPropertyName, $headerName, $order, $isComputed) {
        $this->fieldPropertyName = $fieldPropertyName;
        $this->headerName = $headerName;
        $this->order = $order;
        $this->isComputed = $isComputed;
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

    public function isComputed() {
        return $this->isComputed ? 1 : 0;
    }


} 