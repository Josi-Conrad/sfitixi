<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 11:00
 */

namespace Tixi\CoreDomain\Shared\GenericEntityFilter;


class GenericEntityProperty {
    protected $entityByName;
    protected $propertyByName;
    protected $propertyValue;
    protected $comparingOperator;


    public function __construct($entityByName, $propertyByName, $propertyValue=null, $comparingOperator='=') {
        $this->entityByName = $entityByName;
        $this->propertyByName = $propertyByName;
        $this->propertyValue = $propertyValue;
        $this->comparingOperator = $comparingOperator;
    }

    /**
     * @return mixed
     */
    public function getEntityByName()
    {
        return $this->entityByName;
    }

    /**
     * @return mixed
     */
    public function getPropertyByName()
    {
        return $this->propertyByName;
    }

    /**
     * @return null
     */
    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    public function getEntityPropertyString() {
        return $this->entityByName . '.' . $this->propertyByName;
    }

    /**
     * @return string
     */
    public function getComparingOperator()
    {
        return $this->comparingOperator;
    }



} 