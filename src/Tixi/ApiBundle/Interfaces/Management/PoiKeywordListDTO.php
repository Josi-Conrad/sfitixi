<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 08:59
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;
/**
 * Class PoiKeywordListDTO
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class PoiKeywordListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="PoiKeyword.id")
     */
    public $id;
    /**
     * @GridField(propertyId="PoiKeyword.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="PoiKeyword.name", headerName="poikeyword.field.name", order=1)
     */
    public $name;

    public function getAccessQuery()
    {
        return new GenericAccessQuery('PoiKeyword', 'Tixi\CoreDomain\POIKeyword PoiKeyword', 'PoiKeyword.id');
    }
} 