<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:50
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\MonthlyView;

use Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionPlanListDTO;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class MonthlyPlanListDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo\MonthlyView
 */
class MonthlyPlanListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="WorkingMonth.id")
     */
    public $id;
    /**
     * @GridField(propertyId="WorkingMonth.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="WorkingMonth.date", headerName="monthlyplan.field.date", restrictive=true, comparingOperator=">", order=1)
     */
    public $date;
    /**
     * @GridField(propertyId="WorkingMonth.status", headerName="monthlyplan.field.status", order=3)
     */
    public $status;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('WorkingMonth', 'Tixi\CoreDomain\Dispo\WorkingMonth WorkingMonth', 'WorkingMonth.id');
    }

    /**
     * @param $workingMonthId
     * @return ProductionPlanListDTO
     */
    public static function createReferenceDTOByWorkingMonthId($workingMonthId) {
        $dto = new MonthlyPlanListDTO();
        $dto->id = $workingMonthId;
        $dto->date = new \DateTime('first day of previous month');
        return $dto;
    }
} 