<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class AbsentEmbeddedListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class AbsentEmbeddedListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Absent.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Absent.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Person.id", restrictive=true)
     */
    public $personId;
    /**
     * @GridField(propertyId="Absent.subject", headerName="absent.field.subject", order=1)
     */
    public $subject;
    /**
     * @GridField(propertyId="Absent.startDate", headerName="absent.field.startdate", order=2)
     */
    public $startDate;
    /**
     * @GridField(propertyId="Absent.endDate", headerName="absent.field.enddate", restrictive=true, comparingOperator=">", order=3)
     */
    public $endDate;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('Absent', 'Tixi\CoreDomain\Absent Absent JOIN Absent.person Person', 'Absent.id');
    }

    /**
     * @param $personId
     * @return AbsentEmbeddedListDTO
     */
    public static function createReferenceDTOByPersonId($personId) {
        $dto = new AbsentEmbeddedListDTO();
        $dto->personId = $personId;
        $dto->endDate = new \DateTime('yesterday');
        return $dto;
    }
}