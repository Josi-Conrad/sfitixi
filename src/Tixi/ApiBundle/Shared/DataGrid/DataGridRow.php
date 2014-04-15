<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 23:08
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


class DataGridRow {

    protected $rowId;
    protected $fields;

    public function __construct($rowId) {
        $this->rowId = $rowId;
        $this->fields=array();
    }

    public function appendAndSortFields(array $fieldsArray, $fieldsSortingCallable) {
        $fieldsArrayCopy = $fieldsArray;
        usort($fieldsArrayCopy, $fieldsSortingCallable);
        $this->fields = $fieldsArrayCopy;
    }

    public function getRowId() {
        return $this->rowId;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

//
//    public function getFieldValues() {
//        $fieldValues = array();
//        foreach($this->fields as $field) {
//            $fieldValues[] = $field->getFieldValue();
//        }
//        return $fieldValues;
//    }

} 