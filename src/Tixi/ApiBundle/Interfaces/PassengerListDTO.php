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


class PassengerListDTO implements DataGridSourceClass {
    /**
     * @GridField(propertyId="Passenger.isActive")
     */
    public $isActive;
    /**
     * @GridField(rowIdentifier=true, propertyId="Passenger.id", headerName="passenger.field.id", order=1)
     */
    public $id;
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

    public function getAccessQuery() {
        return new GenericAccessQuery('Passenger', 'Tixi\CoreDomain\Passenger Passenger JOIN Passenger.address Address', 'Passenger.id');
    }
}