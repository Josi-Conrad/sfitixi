<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 10:29
 */

namespace Tixi\CoreDomain\Shared;


use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

interface FastGenericEntityAccessorRepository {

    public function findByFilter(GenericEntityFilter $filter);

} 