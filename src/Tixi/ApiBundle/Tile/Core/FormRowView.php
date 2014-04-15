<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:05
 */

namespace Tixi\ApiBundle\Tile\Core;


class FormRowView {
    protected $fieldId;
    protected $fieldLabelText;
    protected $fieldValue;

    public function __construct($fieldId, $fieldLabel, $fieldValue) {
        $this->fieldId = $fieldId;
        $this->fieldLabelText = $fieldLabel;
        $this->fieldValue = $fieldValue;
    }

    public function getViewParameters() {
        return array('fieldId'=>$this->fieldId);
    }

    /**
     * @return mixed
     */
    public function getFieldId()
    {
        return $this->fieldId;
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