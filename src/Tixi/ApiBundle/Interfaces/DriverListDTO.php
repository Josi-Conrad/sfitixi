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

/**
 * Class DriverListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class DriverListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="Driver.id", headerName="driver.field.id", order=1)
     */
    public $id;
    /**
     * @GridField(propertyId="Driver.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Driver.gender", headerName="person.field.title", order=2)
     */
    public $gender;
    /**
     * @GridField(propertyId="Driver.firstname", headerName="person.field.firstname", order=3)
     */
    public $firstname;
    /**
     * @GridField(propertyId="Driver.lastname", headerName="person.field.lastname", order=4)
     */
    public $lastname;
    /**
     * @GridField(propertyId="Driver.telephone", headerName="person.field.telephone", order=5)
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
    /**
     * @GridField(propertyId="Driver.wheelChairAttendance", headerName="driver.field.wheelchair", order=9)
     */
    public $wheelChairAttendance;
    /**
     * @GridField(propertyId="DriverCategory.name", headerName="driver.field.category", order=10)
     */
    public $driverCategory;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery() {
        return new GenericAccessQuery('Driver', 'Tixi\CoreDomain\Driver Driver LEFT JOIN Driver.driverCategory DriverCategory
        JOIN Driver.address Address', 'Driver.id');
    }
}