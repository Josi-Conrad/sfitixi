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


class DriverListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="Driver.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Driver.isActive")
     */
    public $isActive;
    /**
     * @GridField(propertyId="Driver.title", headerName="Anrede", order=1)
     */
    public $title;
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
     * @GridField(propertyId="DrCat.name", headerName="Kategorie", order=9)
     */
    public $driverCategory;

    public function getAccessQuery() {
        return new GenericAccessQuery('Driver', 'Tixi\CoreDomain\Driver Driver JOIN Driver.driverCategory DrCat
        JOIN Driver.address Address', 'Driver.id');
    }
}