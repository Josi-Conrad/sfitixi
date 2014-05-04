<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class WorkingMonthListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class WorkingMonthListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="WorkingMonth.id")
     */
    public $id;
    /**
     * @GridField(propertyId="WorkingMonth.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="WorkingMonth.date", headerName="workingmonth.field.date", restrictive=true, comparingOperator=">", order=1)
     */
    public $date;
    /**
     * @GridField(propertyId="WorkingMonth.status", headerName="workingmonth.field.status", order=2)
     */
    public $status;
    /**
     * @GridField(propertyId="WorkingMonth.memo", headerName="workingmonth.field.memo",  order=3)
     */
    public $memo;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('WorkingMonth', 'Tixi\CoreDomain\Dispo\WorkingMonth WorkingMonth', 'WorkingMonth.id');
    }

    /**
     * @param $workingMonthId
     * @return WorkingMonthListDTO
     */
    public static function createReferenceDTOByWorkingMonthId($workingMonthId) {
        $dto = new WorkingMonthListDTO();
        $dto->id = $workingMonthId;
        $dto->date = new \DateTime('first day of previous month');
        return $dto;
    }
}