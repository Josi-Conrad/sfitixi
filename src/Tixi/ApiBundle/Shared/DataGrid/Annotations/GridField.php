<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.03.14
 * Time: 21:29
 */

namespace Tixi\ApiBundle\Shared\DataGrid\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class GridField {
    /**
     * @var bool
     */
    public $rowIdentifier = false;
    /**
     * @var bool
     * If set to true, the corresponding property value is
     * included as selective where in the corresponding access query
     */
    public $restrictive = false;
    /**
     * @var string
     * Format: EntityName.PropertyName
     */
    public $propertyId;
    /**
     * @var string
     */
    public $headerName;
    /**
     * @var int
     */
    public $order;

} 