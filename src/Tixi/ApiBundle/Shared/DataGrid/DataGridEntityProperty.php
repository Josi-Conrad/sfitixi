<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 17:29
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty;

/**
 * Class DataGridEntityProperty
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
class DataGridEntityProperty extends GenericEntityProperty{

    /** @var  DataGridEntityPropertyOptions */
    protected $propertyOptions;

    public function __construct($entityByName, $propertyByName, DataGridEntityPropertyOptions $propertyOptions, $propertyValue=null) {
        $this->propertyOptions = $propertyOptions;
        parent::__construct($entityByName, $propertyByName, $propertyValue, $propertyOptions->comparingOperator);
    }

    /**
     * @param array $properties
     * @return array
     */
    public static function getHeaderProperties(array $properties) {
        $headerProperties=array();
        foreach($properties as $property) {
            if($property->isHeader()) {
                $headerProperties[] = $property;
            }
        }
        return $headerProperties;
    }

    /**
     * @param array $properties
     * @return array
     */
    public static function getRestrictiveProperties(array $properties) {
        $restrictiveProperties = array();
        foreach($properties as $property) {
            if($property->isRestrictive()) {
                $restrictiveProperties[] = $property;
            }
        }
        return $restrictiveProperties;
    }

    /**
     * @param array $properties
     * @throws \Exception
     * @return null | DataGridEntityProperty
     */
    public static function getDefaultSortProperty(array $properties) {
        $defaultSortProperty = null;
        /** @var DataGridEntityProperty $property*/
        foreach($properties as $property) {
            if($property->isDefaultSort()) {
                if(null !== $defaultSortProperty) {
                    throw new \Exception('multiple default sort properties found; it is only allowed to set one default property per list.');
                }
                if(!($property->getPropertyOptions()->defaultSort === DataGridEntityPropertyOptions::DEFAULTSORT_ASC
                    || $property->getPropertyOptions()->defaultSort === DataGridEntityPropertyOptions::DEFDAULTSORT_DESC)) {
                    throw new \Exception('missformatted default sort property found. Must match one of the following (case-sensitive): '
                        .DataGridEntityPropertyOptions::DEFAULTSORT_ASC.', '
                        .DataGridEntityPropertyOptions::DEFDAULTSORT_DESC);
                }
                $defaultSortProperty = $property;
            }
        }
        return $defaultSortProperty;
    }

    /**
     * @return boolean
     */
    public function isHeader()
    {
        return $this->propertyOptions->isHeader;
    }

    /**
     * @return boolean
     */
    public function isRestrictive()
    {
        return $this->propertyOptions->isRestrictive;

    }

    public function isDefaultSort()
    {
        return ($this->propertyOptions->defaultSort !== null && $this->propertyOptions->defaultSort !== '');
    }

    public function isComputed() {
        return $this->propertyOptions->isComputed;
    }

    public function getPropertyOptions() {
        return $this->propertyOptions;
    }



} 