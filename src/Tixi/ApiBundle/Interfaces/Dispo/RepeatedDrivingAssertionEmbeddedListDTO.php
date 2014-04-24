<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 11:39
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class RepeatedDrivingAssertionEmbeddedListDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class RepeatedDrivingAssertionEmbeddedListDTO implements DataGridSourceClass {
    /**
     * @GridField(rowIdentifier=true, propertyId="RepeatedDrivingAssertionPlan.id")
     */
    public $id;
    /**
     * @GridField(propertyId="RepeatedDrivingAssertionPlan.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Driver.id", restrictive=true)
     */
    public $driverId;
    /**
     * @GridField(propertyId="RepeatedDrivingAssertionPlan.memo", headerName="repeateddrivingmission.field.memo", order=1)
     */
    public $memo;
    /**
     * @GridField(propertyId="RepeatedDrivingAssertionPlan.anchorDate", headerName="repeateddrivingmission.field.anchordate", order=2)
     */
    public $anchorDate;
    /**
     * @GridField(propertyId="RepeatedDrivingAssertionPlan.endingDate", headerName="repeateddrivingmission.field.endDate", restrictive=true, comparingOperator=">", order=3)
     */
    public $endDate;
    /**
     * @GridField(propertyId="RepeatedDrivingAssertionPlan.frequency", headerName="repeateddrivingmission.field.frequency", order=4)
     */
    public $frequency;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('RepeatedDrivingAssertionPlan', 'Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan RepeatedDrivingAssertionPlan JOIN RepeatedDrivingAssertionPlan.driver Driver', 'RepeatedDrivingAssertionPlan.id');
    }

    /**
     * @param $driverId
     * @return RepeatedDrivingAssertionEmbeddedListDTO
     */
    public static function createReferenceDTOByDriverId($driverId) {
        $dto = new RepeatedDrivingAssertionEmbeddedListDTO();
        $dto->driverId = $driverId;
        $dto->endDate = DateTimeService::getUTCnow();
        return $dto;
    }
}