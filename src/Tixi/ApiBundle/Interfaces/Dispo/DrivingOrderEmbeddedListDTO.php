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
     * @GridField(propertyId="DrivingOrder.pickUpDate", headerName="drivingorder.field.anchordate", restrictive=true, comparingOperator=">", order=1)
     */
    public $pickupDate;
    /**
     * @GridField(propertyId="DrivingOrder.pickUpTime", headerName="drivingorder.field.time", order=2)
     */
    public $pickupTime;
    /**
     * @GridField(headerName="drivingorder.field.lookaheadaddressFrom", isComputed=true, order=3)
     */
    public $addressFromString;
    /**
     * @GridField(headerName="drivingorder.field.lookaheadaddressTo", isComputed=true, order=4)
     */
    public $addressToString;

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
        $dto->pickupDate = new \DateTime('yesterday');
        return $dto;
    }
}