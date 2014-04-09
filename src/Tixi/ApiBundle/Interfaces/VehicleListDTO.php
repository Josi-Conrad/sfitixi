<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 04.03.14
 * Time: 00:31
 */

namespace Tixi\ApiBundle\Interfaces;

use JMS\Serializer\Annotation\SerializedName;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;


class VehicleListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="Vehicle.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Vehicle.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Vehicle.name", headerName="vehicle.field.name", order=1)
     */
    public $name;
    /**
     * @SerializedName("licenceNumber")
     * @GridField(propertyId="Vehicle.licenceNumber", headerName="vehicle.field.licencenumber", order=2)
     */
    public $licenceNumber;
    /**
     * @GridField(propertyId="VehicleCategory.name", headerName="vehicle.field.category", order=4)
     */
    public $category;
    /**
     * @GridField(propertyId="VehicleCategory.name", headerName="vehicle.field.category.amountofseats", order=5)
     */
    public $amountOfSeats;
    /**
     * @GridField(propertyId="VehicleCategory.amountOfWheelChairs", headerName="vehicle.field.category.amountofwheelchairs", order=6)
     */
    public $amountOfWheelChairs;
    /**
     * @GridField(propertyId="Vehicle.parking", headerName="vehicle.field.parking", order=7)
     */
    public $parking;
    /**
     * @GridField(propertyId="Vehicle.dateOfFirstRegistration", headerName="vehicle.field.dateoffirstregistration", order=8)
     */
    public $dateOfFirstRegistration;

    public function getAccessQuery() {
        return new GenericAccessQuery('Vehicle', 'Tixi\CoreDomain\Vehicle Vehicle JOIN Vehicle.category VehicleCategory', 'Vehicle.id');
    }
}