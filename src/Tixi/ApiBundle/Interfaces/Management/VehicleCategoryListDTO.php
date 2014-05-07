<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 14:08
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class VehicleCategoryListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class VehicleCategoryListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="VehicleCategory.id")
     */
    public $id;
    /**
     * @GridField(propertyId="VehicleCategory.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="VehicleCategory.name", headerName="vehiclecategory.field.name", order=1)
     */
    public $name;
    /**
     * @GridField(propertyId="VehicleCategory.amountOfSeats", headerName="vehiclecategory.field.amountofseats", order=2)
     */
    public $amountOfSeats;
    /**
     * @GridField(propertyId="VehicleCategory.amountOfWheelChairs", headerName="vehiclecategory.field.amountofwheelchairs", order=3)
     */
    public $amountOfWheelChairs;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('VehicleCategory', 'Tixi\CoreDomain\VehicleCategory VehicleCategory', 'VehicleCategory.id');
    }
}