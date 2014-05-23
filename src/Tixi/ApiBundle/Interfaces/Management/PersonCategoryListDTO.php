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
 * Class PersonCategoryListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class PersonCategoryListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="PersonCategory.id")
     */
    public $id;
    /**
     * @GridField(propertyId="PersonCategory.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="PersonCategory.name", headerName="personcategory.field.name", order=1)
     */
    public $name;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('PersonCategory', 'Tixi\CoreDomain\PersonCategory PersonCategory', 'PersonCategory.id');
    }
}