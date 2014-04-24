<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class DriverCategoryListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class DriverCategoryListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="DriverCategory.id")
     */
    public $id;
    /**
     * @GridField(propertyId="DriverCategory.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="DriverCategory.name", headerName="drivercategory.field.name", order=1)
     */
    public $name;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('DriverCategory', 'Tixi\CoreDomain\DriverCategory DriverCategory', 'DriverCategory.id');
    }
}