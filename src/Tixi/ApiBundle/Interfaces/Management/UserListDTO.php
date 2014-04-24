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
 * Class UserListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class UserListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="User.id")
     */
    public $id;
    /**
     * @GridField(propertyId="User.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="User.username", headerName="user.field.username", order=1)
     */
    public $username;
    /**
     * @GridField(propertyId="User.email", headerName="user.field.email", order=2)
     */
    public $email;
    /**
     * @GridField(propertyId="Role.name", headerName="user.field.roles", order=3)
     */
    public $roles;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('User', 'Tixi\SecurityBundle\Entity\User User JOIN User.roles Role', 'User.id');
    }
}