<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 21:43
 */

namespace Tixi\ApiBundle\Shared\DataGrid;

use Doctrine\Common\Annotations\FileCacheReader;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\ApiBundle\Shared\DataGrid\RESTHandler\DataGridState;
use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\OrderBy;
use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\Search;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty;

class DataGrid {

    protected $reader;
    protected $rowIdAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\GridRowId';
    protected $fieldAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField';
    protected $headerClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridHeader';
    protected $fieldClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridField';

    public function createGenericEntityFilterByState(DataGridState $state) {
        $filter = new GenericEntityFilter($state->getSourceDTO()->getAccessQuery());
        $properties = $this->createEntityPropertiesArray($state->getSourceDTO());
        $restrictiveProperties = DataGridEntityProperty::getRestrictiveProperties($properties);
        $headerProperties = DataGridEntityProperty::getHeaderProperties($properties);
        if(count($restrictiveProperties)>0) {
            $filter->setRestrictiveProperties($restrictiveProperties);
        }
        if(null !== $state->getOrderByDirection() && null !== $state->getOrderByField()) {
            $orderFieldExploded = explode('.',$state->getOrderByField());
            if(count($orderFieldExploded)<2) {throw new \Exception('misformatted propertyId found on field '.$state->getOrderByField());}
            $filter->setOrderedBy(new OrderBy(new GenericEntityProperty($orderFieldExploded[0],$orderFieldExploded[1]), $state->getOrderByDirection()));
        }
        if(null !== $state->getFilterStr()) {
            $filter->setSearch(new Search($state->getFilterStr(), $headerProperties));
        }
        if(null !== $state->getPage() && null !== $state->getLimit()) {
            $filter->setOffset($state->getPage()*$state->getLimit());
        }
        if(null !== $state->getLimit()) {
            $filter->setLimit($state->getLimit());
        }
        return $filter;
    }

    protected function createEntityPropertiesArray(DataGridSourceClass $sourceClassInstance) {
        $sourceReflectionObject = new \ReflectionClass($sourceClassInstance);
        $properties = array();

        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(!empty($dataGridFieldAnnotation->propertyId)) {
                $explodedPropertyId = explode('.', $dataGridFieldAnnotation->propertyId);
                if(count($explodedPropertyId)<2) {throw new \Exception('misformatted propertyId found on field '.$reflProperty->getName());}
                $entityName = $explodedPropertyId[0];
                $propertyName = $explodedPropertyId[1];
                $isRestrictive = $dataGridFieldAnnotation->restrictive;
                $isHeader = !empty($dataGridFieldAnnotation->headerName);
                $propertyValue = $reflProperty->getValue($sourceClassInstance);
                $properties[] = new DataGridEntityProperty($entityName, $propertyName, $propertyValue, $isRestrictive, $isHeader);
            }
        }
        return $properties;
    }



    public function createHeaderArray(DataGridSourceClass $sourceClassInstance) {
        $sourceReflectionObject = new \ReflectionClass($sourceClassInstance);
        $headers = array();

        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridFieldAnnotation) {
                if(!empty($dataGridFieldAnnotation->headerName)) {
                    $rowId = $dataGridFieldAnnotation->propertyId;
                    if(null === $rowId) {throw new \Exception('no propertyId found on field '.$reflProperty->getName());}
                    $headers[] = new DataGridHeader($rowId, $dataGridFieldAnnotation->headerName, $dataGridFieldAnnotation->order);
                }
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

    protected function createRow(DataGridSourceClass $source) {
        $sourceReflectionObject = new \ReflectionClass($source);
        $rowId = null;
        $fields = array();
        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->reader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridFieldAnnotation) {
                if($dataGridFieldAnnotation->rowIdentifier) {
                    if(null !== $rowId) {throw new \Exception('found duplicated row id');}
                    $rowId = $reflProperty->getValue($source);
                }
                if(!empty($dataGridFieldAnnotation->headerName)) {
                    $fields[] = new DataGridField($reflProperty->getName(), $reflProperty->getValue($source), $dataGridFieldAnnotation->order);
                }
            }
        }
        if(null === $rowId) {throw new \Exception('no row id found');}
        $row = new DataGridRow($rowId);
        $row->appendAndSortFields($fields, $this->fieldClass . '::compare');
        return $row;
    }

    public function setReader(FileCacheReader $reader) {
        $this->reader = $reader;
    }



} 