<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 30.04.14
 * Time: 11:39
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class DrivingOrderEmbeddedListDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class DrivingOrderEmbeddedListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="DrivingOrder.id")
     */
    public $id;
    /**
     * @GridField(propertyId="DrivingOrder.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Passenger.id", restrictive=true)
     */
    public $passengerId;
    /**
     * @GridField(propertyId="DrivingOrder.memo", headerName="drivingorder.field.memo", order=1)
     */
    public $memo;
    /**
     * @GridField(propertyId="DrivingOrder.anchorDate", headerName="drivingorder.field.anchordate", order=2)
     */
    public $pickupDate;
    /**
     * @GridField(propertyId="DrivingOrder.endingDate", headerName="drivingorder.field.endDate", restrictive=true, comparingOperator=">", order=3)
     */
    public $pickupTime;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('DrivingOrder', 'Tixi\CoreDomain\Dispo\DrivingOrder DrivingOrder JOIN DrivingOrder.passenger Passenger', 'DrivingOrder.id');
    }

    /**
     * @param $passengerId
     * @return DrivingOrderEmbeddedListDTO
     */
    public static function createReferenceDTOByDriverId($passengerId) {
        $dto = new DrivingOrderEmbeddedListDTO();
        $dto->passengerId = $passengerId;
        return $dto;
    }
}