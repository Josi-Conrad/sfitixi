<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 10:29
 */

namespace Tixi\CoreDomain\Shared;


use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * Interface FastGenericEntityAccessorRepository
 * @package Tixi\CoreDomain\Shared
 */
interface FastGenericEntityAccessorRepository {
    /**
     * @param GenericEntityFilter $filter
     * @return mixed
     */
    public function findByFilter(GenericEntityFilter $filter);

} 