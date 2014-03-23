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
     * @GridField(rowIdentifier=true, propertyId="Passenger.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Passenger.isActive")
     */
    public $isActive;
    /**
     * @GridField(propertyId="Passenger.title", headerName="Anrede", order=1)
     */
    public $title;
    /**
     * @GridField(propertyId="Passenger.firstname", headerName="Vorname", order=3)
     */
    public $firstname;
    /**
     * @GridField(propertyId="Passenger.lastname", headerName="Nachname", order=4)
     */
    public $lastname;
    /**
     * @GridField(propertyId="Passenger.telephone", headerName="Telefon-Nr", order=5)
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
     * @GridField(propertyId="Handicap.name", headerName="Behinderung", order=9)
     */
    public $handicap;

    public function getAccessQuery() {
        return new GenericAccessQuery('Passenger', 'Tixi\CoreDomain\Passenger Passenger JOIN Passenger.handicap Handicap
        JOIN Passenger.address Address', 'Passenger.id');
    }
}