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


class DriverListDTO implements DataGridSourceClass {
    /**
     * @GridField(propertyId="Driver.isActive")
     */
    public $isActive;
    /**
     * @GridField(rowIdentifier=true, propertyId="Driver.id", headerName="Fahrer-Nr", order=1)
     */
    public $id;
    /**
     * @GridField(propertyId="Driver.firstname", headerName="Vorname", order=3)
     */
    public $firstname;
    /**
     * @GridField(propertyId="Driver.lastname", headerName="Nachname", order=4)
     */
    public $lastname;
    /**
     * @GridField(propertyId="Driver.telephone", headerName="Telefon-Nr", order=5)
     */
    public $telephone;
    /**
     * @GridField(propertyId="Address.street", headerName="Strasse", order=7)
     */
    public $street;
    /**
     * @GridField(propertyId="Address.city", headerName="Ort", order=8)
     */
    public $city;
    /**
     * @GridField(propertyId="DriverCategory.name", headerName="Kategorie", order=9)
     */
    public $driverCategory;

    public function getAccessQuery() {
        return new GenericAccessQuery('Driver', 'Tixi\CoreDomain\Driver Driver JOIN Driver.driverCategory DriverCategory
        JOIN Driver.address Address', 'Driver.id');
    }
}