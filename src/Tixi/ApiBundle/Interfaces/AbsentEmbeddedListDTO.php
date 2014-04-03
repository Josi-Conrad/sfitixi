<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

class AbsentEmbeddedListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Absent.id")
     */
    public $id;
    /**
     * @GridField(propertyId="person.id", restrictive=true)
     */
    public $personId;
    /**
     * @GridField(propertyId="Absent.subject", headerName="Beschreibung", order=1)
     */
    public $subject;
    /**
     * @GridField(propertyId="Absent.startDate", headerName="Start Datum", order=2)
     */
    public $startDate;
    /**
     * @GridField(propertyId="Absent.endDate", headerName="End Datum", order=3)
     */
    public $endDate;


    public function getAccessQuery()
    {
        return new GenericAccessQuery('Absent', 'Tixi\CoreDomain\Absent Absent JOIN Absent.person Person', 'Absent.id');
    }

    public static function createReferenceDTOByPersonId($personId) {
        $dto = new AbsentEmbeddedListDTO();
        $dto->personId = $personId;
        return $dto;
    }
}