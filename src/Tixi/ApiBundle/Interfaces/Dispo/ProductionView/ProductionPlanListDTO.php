<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 14:59
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\ProductionView;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

class ProductionPlanListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="WorkingMonth.id")
     */
    public $id;
    /**
     * @GridField(propertyId="WorkingMonth.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="WorkingMonth.date", headerName="productionplan.field.date", restrictive=true, comparingOperator=">", order=1)
     */
    public $date;

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
        $dto = new ProductionPlanListDTO();
        $dto->id = $workingMonthId;
        $dto->date = new \DateTime('first day of previous month');
        return $dto;
    }
}