<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 17.04.14
 * Time: 09:46
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class ZoneListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class ZoneListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Zone.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Zone.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Zone.name", headerName="zone.field.name", order=1)
     */
    public $name;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('Zone', 'Tixi\CoreDomain\Zone Zone', 'Zone.id');
    }

} 