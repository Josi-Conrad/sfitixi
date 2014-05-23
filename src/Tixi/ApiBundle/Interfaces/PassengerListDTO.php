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
 * Class PassengerListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class PassengerListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="Passenger.id", headerName="passenger.field.id", order=1)
     */
    public $id;
    /**
     * @GridField(propertyId="Passenger.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Passenger.gender", headerName="person.field.title", order=2)
     */
    public $gender;
    /**
     * @GridField(propertyId="Passenger.firstname", headerName="person.field.firstname", order=3)
     */
    public $firstname;
    /**
     * @GridField(propertyId="Passenger.lastname", headerName="person.field.lastname", order=4)
     */
    public $lastname;
    /**
     * @GridField(propertyId="Passenger.telephone", headerName="person.field.telephone", order=5)
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
     * @GridField(propertyId="Passenger.isInWheelChair", headerName="passenger.field.isinwheelchair", order=9)
     */
    public $isInWheelChair;
    /**
     * @GridField(propertyId="Passenger.hasMonthlyBilling", headerName="passenger.field.payment", order=10)
     */
    public $hasMonthlyBilling;
    /**
     * @GridField(propertyId="Insurance.name", headerName="passenger.field.insurance", order=11)
     */
    public $insurances;

    public function getAccessQuery() {
        return new GenericAccessQuery('Passenger', 'Tixi\CoreDomain\Passenger Passenger JOIN Passenger.address Address
        LEFT JOIN Passenger.insurances Insurance', 'Passenger.id');
    }
}