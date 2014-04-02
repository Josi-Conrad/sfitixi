<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:05
 */

namespace Tixi\ApiBundle\Tile\Core;


class FormRowView {
    protected $fieldLabelText;
    protected $fieldValue;

    public function __construct($fieldLabel, $fieldValue) {
        $this->fieldLabelText = $fieldLabel;
        $this->fieldValue = $fieldValue;
    }

    /**
     * @return mixed
     */
    public function getFieldLabelText()
    {
        return $this->fieldLabelText;
    }

    /**
     * @return mixed
     */
    public function getFieldValue()
    {
        return $this->fieldValue;
    }


} 