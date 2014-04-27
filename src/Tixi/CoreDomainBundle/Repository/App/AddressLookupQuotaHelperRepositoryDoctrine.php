<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 10:02
 */

namespace Tixi\CoreDomainBundle\Repository\App;


use Tixi\CoreDomain\App\AddressLookupQuotaHelper;
use Tixi\CoreDomain\App\AddressLookupQuotaHelperRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class AddressLookupQuotaHelperRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements AddressLookupQuotaHelperRepository{

    /**
     * @param AddressLookupQuotaHelper $addressLookupQuotaHelper
     * @return mixed
     */
    public function store(AddressLookupQuotaHelper $addressLookupQuotaHelper)
    {
        $this->getEntityManager()->persist($addressLookupQuotaHelper);
    }

    /**
     * @param AddressLookupQuotaHelper $addressLookupQuotaHelper
     * @return mixed
     */
    public function remove(AddressLookupQuotaHelper $addressLookupQuotaHelper)
    {
        $this->getEntityManager()->remove($addressLookupQuotaHelper);
    }
}