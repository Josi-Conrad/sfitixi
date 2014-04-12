<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class ServicePlanEmbeddedListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class ServicePlanEmbeddedListDTO implements DataGridSourceClass{

    /**
     * @GridField(rowIdentifier=true, propertyId="ServicePlan.id")
     */
    public $id;
    /**
     * @GridField(propertyId="ServicePlan.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Vehicle.id", restrictive=true)
     */
    public $vehicleId;
    /**
     * @GridField(propertyId="ServicePlan.startDate", headerName="serviceplan.field.startdate", order=1)
     */
    public $startDate;
    /**
     * @GridField(propertyId="ServicePlan.endDate", headerName="serviceplan.field.enddate", order=2)
     */
    public $endDate;
    /**
     * @GridField(propertyId="ServicePlan.memo", headerName="serviceplan.field.memo", order=3)
     */
    public $memo;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('ServicePlan', 'Tixi\CoreDomain\ServicePlan ServicePlan JOIN ServicePlan.vehicle Vehicle', 'ServicePlan.id');
    }

    /**
     * @param $vehicleId
     * @return ServicePlanEmbeddedListDTO
     */
    public static function createReferenceDTOByVehicleId($vehicleId) {
        $dto = new ServicePlanEmbeddedListDTO();
        $dto->vehicleId = $vehicleId;
        return $dto;
    }
}