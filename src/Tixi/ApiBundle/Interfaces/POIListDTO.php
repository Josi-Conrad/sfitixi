<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Interfaces;

use JMS\Serializer\Annotation\SerializedName;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;


class POIListDTO implements DataGridSourceClass {
    /**
     * @GridField(propertyId="POI.isActive")
     */
    public $isActive;
    /**
     * @GridField(rowIdentifier=true, propertyId="POI.id", headerName="poi.field.id", order=1)
     */
    public $id;
    /**
     * @GridField(propertyId="POI.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="POI.name", headerName="poi.field.name", order=3)
     */
    public $name;
    /**
     * @GridField(propertyId="POI.department", headerName="poi.field.department", order=4)
     */
    public $department;
    /**
     * @GridField(propertyId="POI.telephone", headerName="poi.field.telephone", order=5)
     */
    public $telephone;
    /**
     * @GridField(propertyId="Address.street", headerName="address.field.street", order=7)
     */
    public $street;
    /**
     * @GridField(propertyId="Address.city", headerName="address.field.city", order=8)
     */
    public $city;

    public function getAccessQuery() {
        return new GenericAccessQuery('POI', 'Tixi\CoreDomain\POI POI JOIN POI.address Address', 'POI.id');
    }
}