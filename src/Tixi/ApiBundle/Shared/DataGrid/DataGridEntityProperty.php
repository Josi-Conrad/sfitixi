<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 17:29
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty;

class DataGridEntityProperty extends GenericEntityProperty{
    protected $isRestrictive;
    protected $isHeader;

    public function __construct($entityByName, $propertyByName, $comparingOperator='=', $propertyValue=null, $isRestrictive=false, $isHeader=false) {
        parent::__construct($entityByName, $propertyByName, $propertyValue, $comparingOperator);
        $this->isRestrictive = $isRestrictive;
        $this->isHeader = $isHeader;
    }

    public static function getHeaderProperties(array $properties) {
        $headerProperties=array();
        foreach($properties as $property) {
            if($property->isHeader()) {
                $headerProperties[] = $property;
            }
        }
        return $headerProperties;
    }

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
     * @return boolean
     */
    public function isHeader()
    {
        return $this->isHeader;
    }

    /**
     * @return boolean
     */
    public function isRestrictive()
    {
        return $this->isRestrictive;
    }


} 