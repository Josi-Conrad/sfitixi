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

class ServicePlanEmbeddedListDTO implements DataGridSourceClass{

    /**
     * @GridField(rowIdentifier=true, propertyId="ServicePlan.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Vehicle.id", restrictive=true)
     */
    public $vehicleId;
    /**
     * @GridField(propertyId="ServicePlan.startDate", headerName="Start Datum", order=1)
     */
    public $startDate;
    /**
     * @GridField(propertyId="ServicePlan.endDate", headerName="End Datum", order=2)
     */
    public $endDate;
    /**
     * @GridField(propertyId="ServicePlan.cost", headerName="Kosten", order=3)
     */
    public $cost;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('ServicePlan', 'Tixi\CoreDomain\ServicePlan ServicePlan JOIN ServicePlan.vehicle Vehicle', 'ServicePlan.id');
    }

    public static function createReferenceDTOByVehicleId($vehicleId) {
        $dto = new ServicePlanEmbeddedListDTO();
        $dto->vehicleId = $vehicleId;
        return $dto;
    }
}