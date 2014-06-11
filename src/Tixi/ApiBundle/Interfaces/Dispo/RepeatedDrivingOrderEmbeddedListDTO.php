<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.06.14
 * Time: 22:49
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;
use Tixi\ApiBundle\Helper\DateTimeService;

class RepeatedDrivingOrderEmbeddedListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="RepeatedDrivingOrderPlan.id")
     */
    public $id;
    /**
     * @GridField(propertyId="RepeatedDrivingOrderPlan.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Passenger.id", restrictive=true)
     */
    public $passengerId;
    /**
     * @GridField(propertyId="RepeatedDrivingOrderPlan.anchorDate", headerName="repeateddrivingorder.field.anchordate", order=1)
     */
    public $anchorDate;
    /**
     * @GridField(propertyId="RepeatedDrivingOrderPlan.endingDate", headerName="repeateddrivingorder.field.endDate", restrictive=true, comparingOperator=">", order=2)
     */
    public $endDate;
    /**
     * @GridField(propertyId="RepeatedDrivingOrderPlan.memo", headerName="drivingorder.field.memo", order=3)
     */
    public $memo;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('RepeatedDrivingOrderPlan', 'Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan RepeatedDrivingOrderPlan JOIN RepeatedDrivingOrderPlan.passenger Passenger', 'RepeatedDrivingOrderPlan.id');
    }

    public static function createReferenceDTOByPassengerId($passengerId) {
        $dto = new RepeatedDrivingOrderEmbeddedListDTO();
        $dto->passengerId = $passengerId;
        $dto->endDate = DateTimeService::getUTCnow();
        return $dto;
    }
} 