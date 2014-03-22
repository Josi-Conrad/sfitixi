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


class VehicleListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Vehicle.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Vehicle.name", headerName="Name", order=1)
     */
    public $name;
    /**
     * @SerializedName("licenceNumber")
     * @GridField(propertyId="Vehicle.licenceNumber", headerName="Lizenznummer", order=2)
     */
    public $licenceNumber;
    /**
     * @GridField(propertyId="Vehicle.licenceNumber", headerName="Inverkehrssetzung", order=5)
     */
    public $dateOfFirstRegistration;
    /**
     * @GridField(propertyId="Vehicle.parkingLotNumber", headerName="Parkplatz Nummer", order=3)
     */
    public $parkingLotNumber;
    /**
     * @GridField(propertyId="VehicleCategory.name", headerName="Fahrzeugkategorie", order=4)
     */
    public $category;


    public function getAccessQuery()
    {
        return new GenericAccessQuery('Vehicle', 'Tixi\CoreDomain\Vehicle Vehicle JOIN Vehicle.category VehicleCategory', 'Vehicle.id');
    }
}