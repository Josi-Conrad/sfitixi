<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 23:13
 */

namespace Tixi\ApiBundle\Shared\DataGrid;

/**
 * Class DataGridField
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
class DataGridField {
    private $fieldPropertyName;
    private $fieldValue;
    private $fieldOrder;

    /**
     * @param $fieldPropertyName
     * @param $fieldValue
     * @param $fieldOrder
     */
    public function __construct($fieldPropertyName, $fieldValue, $fieldOrder)
    {
        $this->fieldPropertyName = $fieldPropertyName;
        $this->fieldValue = $fieldValue;
        $this->fieldOrder = $fieldOrder;

    }

    /**
     * @param DataGridField $a
     * @param DataGridField $b
     * @return mixed
     */
    public static function compare(DataGridField $a, DataGridField $b) {
        return $a->fieldOrder - $b->fieldOrder;
    }

    /**
     * @return mixed
     */
    public function getFieldValue() {
        return $this->fieldValue;
    }

    /**
     * @return mixed
     */
    public function getFieldPropertyName()
    {
        return $this->fieldPropertyName;
    }


} 