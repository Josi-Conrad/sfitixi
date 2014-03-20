<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 23:13
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


class DataGridField {
    private $fieldPropertyName;
    private $fieldValue;
    private $fieldOrder;

    public function __construct($fieldPropertyName, $fieldValue, $fieldOrder)
    {
        $this->fieldPropertyName = $fieldPropertyName;
        $this->fieldValue = $fieldValue;
        $this->fieldOrder = $fieldOrder;

    }

    public static function compare(DataGridField $a, DataGridField $b) {
        return $a->fieldOrder - $b->fieldOrder;
    }

    public function getFieldValue() {
        return $this->fieldValue;
    }
} 