<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:51
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

class MonthlyPlanWorkingDayListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="WorkingDay.id")
     */
    public $id;
    /**
     * @GridField(propertyId="WorkingMonth.id", restrictive=true)
     */
    public $workingMonthId;
    /**
     * @GridField(propertyId="WorkingDay.date", headerName="monthlyplan.workingday.field.datestring")
     */
    public $dateString;
    /**
     * @GridField(headerName="monthlyplan.workingday.field.weekdaystring", isComputed=true)
     */
    public $weekDayString;

    /**
     * @return mixed
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('WorkingDay', 'Tixi\CoreDomain\Dispo\WorkingDay WorkingDay JOIN WorkingDay.workingMonth WorkingMonth', 'WorkingDay.id');
    }

    /**
     * @param $workingMonthId
     * @return MonthlyPlanWorkingDayListDTO
     */
    public static function createReferenceDTOByWorkingDayId($workingMonthId) {
        $dto = new MonthlyPlanWorkingDayListDTO();
        $dto->workingMonthId = $workingMonthId;
        return $dto;
    }
}