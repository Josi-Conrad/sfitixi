<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 21:43
 */

namespace Tixi\ApiBundle\Shared\DataGrid;;


use Doctrine\Common\Annotations\AnnotationReader;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;

class DataGrid {

    protected $reader;
    protected $rowIdAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\DataGridRowId';
    protected $fieldAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\DataGridField';
    protected $headerClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridHeader';
    protected $fieldClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridField';


    public function __construct($reader) {
        $this->reader = $reader;
    }

    public function createHeaderArray(DataGridSourceClass $sourceClassInstance) {
        $sourceReflectionObject = new \ReflectionClass($sourceClassInstance);
        $headers = array();

        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridFieldAnnotation) {
                $headers[] = new DataGridHeader($reflProperty->getName(), $dataGridFieldAnnotation->headerName, $dataGridFieldAnnotation->order);
            }
        }
        usort($headers, $this->headerClass . '::compare');
        return $headers;
    }

    public function createRowsArray(array $sourceArray) {
        $rowsArray = array();
        foreach($sourceArray as $rowSource) {
            $row = $this->createRow($rowSource);
            $rowsArray[] = array('rowId'=>$row->getRowId(),'fieldValues'=>$row->getFieldValues());
        }
        return $rowsArray;
    }

    protected  function createRow(DataGridSourceClass $source) {
        $sourceReflectionObject = new \ReflectionClass($source);
        $rowId = null;
        $fields = array();
        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridRowIdAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->rowIdAnnotationClass);
            $dataGridFieldAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridRowIdAnnotation) {
                if(null !== $rowId) {throw new \Exception('found duplicated row id');}
                $rowId = $reflProperty->getValue($source);
            }
            if(null !== $dataGridFieldAnnotation) {
                $fields[] = new DataGridField($reflProperty->getName(), $reflProperty->getValue($source), $dataGridFieldAnnotation->order);
            }
        }
        if(null === $rowId) {throw new \Exception('no row id found');}
        $row = new DataGridRow($rowId);
        $row->appendAndSortFields($fields, $this->fieldClass . '::compare');
        return $row;
    }

} 