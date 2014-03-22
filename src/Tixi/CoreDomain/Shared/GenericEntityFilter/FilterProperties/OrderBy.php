<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 11:02
 */

namespace Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties;


use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty;

class OrderBy {

    /**
     * @var string
     */
    const ASC  = 'ASC';
    /**
     * @var string
     */
    const DESC = 'DESC';
    /**
     * @var \Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty
     */
    protected $entityProperty;
    /**
     * @var string
     */
    protected $direction;

    public function __construct(GenericEntityProperty $entityProperty, $direction) {
        $this->entityProperty = $entityProperty;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return \Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty
     */
    public function getEntityProperty()
    {
        return $this->entityProperty;
    }

} 