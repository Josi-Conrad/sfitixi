<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 10:00
 */

namespace Tixi\CoreDomain\App;


use Tixi\CoreDomain\Shared\CommonBaseRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

interface AddressLookupQuotaHelperRepository extends CommonBaseRepository{
    /**
     * @param AddressLookupQuotaHelper $addressLookupQuotaHelper
     * @return mixed
     */
    public function store(AddressLookupQuotaHelper $addressLookupQuotaHelper);

    /**
     * @param AddressLookupQuotaHelper $addressLookupQuotaHelper
     * @return mixed
     */
    public function remove(AddressLookupQuotaHelper $addressLookupQuotaHelper);
} 