<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:46
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class HandicapListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class HandicapListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Handicap.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Handicap.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Handicap.name", headerName="handicap.field.name", order=1)
     */
    public $name;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('Handicap', 'Tixi\CoreDomain\Handicap Handicap', 'Handicap.id');
    }

} 