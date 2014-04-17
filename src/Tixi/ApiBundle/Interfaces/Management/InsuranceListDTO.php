<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 10:27
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class InsuranceListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class InsuranceListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="Insurance.id")
     */
    public $id;
    /**
     * @GridField(propertyId="Insurance.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="Insurance.name", headerName="insurance.field.name", order=1)
     */
    public $name;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('Insurance', 'Tixi\CoreDomain\Insurance Insurance', 'Insurance.id');
    }
} 