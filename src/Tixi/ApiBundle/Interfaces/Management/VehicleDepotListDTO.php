<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:46
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class VehicleDepotListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class VehicleDepotListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="VehicleDepot.id")
     */
    public $id;
    /**
     * @GridField(propertyId="VehicleDepot.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="VehicleDepot.name", headerName="vehicledepot.field.name", order=1)
     */
    public $name;
    /**
     * @GridField(propertyId="Address.street", headerName="address.field.street", order=7)
     */
    public $street;
    /**
     * @GridField(propertyId="Address.city", headerName="address.field.city", order=8)
     */
    public $city;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('VehicleDepot', 'Tixi\CoreDomain\VehicleDepot VehicleDepot JOIN VehicleDepot.address Address', 'VehicleDepot.id');
    }

} 