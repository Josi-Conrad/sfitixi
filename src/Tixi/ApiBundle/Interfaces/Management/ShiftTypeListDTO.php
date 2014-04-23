<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class ShiftTypeListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class ShiftTypeListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="ShiftType.id")
     */
    public $id;
    /**
     * @GridField(propertyId="ShiftType.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="ShiftType.name", headerName="shifttype.field.name", order=1)
     */
    public $name;
    /**
     * @GridField(propertyId="ShiftType.start", headerName="shifttype.field.start", order=2)
     */
    public $start;
    /**
     * @GridField(propertyId="ShiftType.end", headerName="shifttype.field.end", order=3)
     */
    public $end;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('ShiftType', 'Tixi\CoreDomain\Dispo\ShiftType ShiftType', 'ShiftType.id');
    }
}