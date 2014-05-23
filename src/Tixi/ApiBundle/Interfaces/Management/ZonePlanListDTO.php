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
 * Class ZonePlanListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class ZonePlanListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="ZonePlan.id")
     */
    public $id;
    /**
     * @GridField(propertyId="ZonePlan.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="ZonePlan.city", headerName="zoneplan.field.city", order=1)
     */
    public $city;
    /**
     * @GridField(propertyId="Zone.name", headerName="zoneplan.field.zone", order=2)
     */
    public $zoneName;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('ZonePlan', 'Tixi\CoreDomain\ZonePlan ZonePlan LEFT JOIN ZonePlan.zone Zone', 'ZonePlan.id');
    }

} 