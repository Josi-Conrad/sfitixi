<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 23:08
 */

namespace Tixi\ApiBundle\Shared\DataGrid;

/**
 * Class DataGridRow
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
class DataGridRow {

    protected $rowId;
    protected $fields;

    /**
     * @param $rowId
     */
    public function __construct($rowId) {
        $this->rowId = $rowId;
        $this->fields=array();
    }

    /**
     * @param array $fieldsArray
     * @param $fieldsSortingCallable
     */
    public function appendAndSortFields(array $fieldsArray, $fieldsSortingCallable) {
        $fieldsArrayCopy = $fieldsArray;
        usort($fieldsArrayCopy, $fieldsSortingCallable);
        $this->fields = $fieldsArrayCopy;
    }

    /**
     * @return mixed
     */
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
} 