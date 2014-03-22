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


    public function __construct($entityByName, $propertyByName, $propertyValue=null) {
        $this->entityByName = $entityByName;
        $this->propertyByName = $propertyByName;
        $this->propertyValue = $propertyValue;
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

} 