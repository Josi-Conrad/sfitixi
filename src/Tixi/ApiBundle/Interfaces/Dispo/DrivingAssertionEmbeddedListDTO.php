<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.05.14
 * Time: 10:59
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class DrivingAssertionEmbeddedListDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class DrivingAssertionEmbeddedListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="DrivingAssertion.id")
     */
    public $id;
    /**
     * @GridField(propertyId="DrivingAssertion.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Driver.id", restrictive=true)
     */
    public $driverId;
    /**
     * @GridField(headerName="drivingassertion.field.date", isComputed=true, order=1)
     */
    public $date;
    /**
     * @GridField(headerName="drivingassertion.field.shift", isComputed=true, order=2)
     */
    public $shift;

    public $dateAsDateTime;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('DrivingAssertion', 'Tixi\CoreDomain\Dispo\DrivingAssertion DrivingAssertion JOIN DrivingAssertion.driver Driver', 'DrivingAssertion.id');
    }

    /**
     * @param $driverId
     * @return DrivingAssertionEmbeddedListDTO
     */
    public static function createReferenceDTOByDriverId($driverId) {
        $dto = new DrivingAssertionEmbeddedListDTO();
        $dto->driverId = $driverId;
        return $dto;
    }
} 