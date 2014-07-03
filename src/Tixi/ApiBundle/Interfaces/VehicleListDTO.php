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

/**
 * Class VehicleListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
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
     * @GridField(propertyId="Vehicle.name", headerName="vehicle.field.name", order=1, defaultSort="ASC")
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
     * @GridField(propertyId="VehicleCategory.amountOfSeats", headerName="vehiclecategory.field.amountofseats", order=5)
     */
    public $amountOfSeats;
    /**
     * @GridField(propertyId="VehicleCategory.amountOfWheelChairs", headerName="vehiclecategory.field.amountofwheelchairs", order=6)
     */
    public $amountOfWheelChairs;
    /**
     * @GridField(propertyId="VehicleDepot.name", headerName="vehicle.field.depot", order=7)
     */
    public $depot;
    /**
     * @GridField(propertyId="Vehicle.parking", headerName="vehicle.field.parking", order=8)
     */
    public $parking;
    /**
     * @GridField(propertyId="Vehicle.dateOfFirstRegistration", headerName="vehicle.field.dateoffirstregistration", order=9)
     */
    public $dateOfFirstRegistration;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery() {
        return new GenericAccessQuery('Vehicle', 'Tixi\CoreDomain\Vehicle Vehicle JOIN Vehicle.category VehicleCategory
        JOIN Vehicle.depot VehicleDepot', 'Vehicle.id');
    }
}