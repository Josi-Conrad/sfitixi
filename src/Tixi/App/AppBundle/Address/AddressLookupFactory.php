<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 22:26
 */

namespace Tixi\App\AppBundle\Address;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\CoreDomain\App\AddressLookupQuotaHelper;
use Tixi\CoreDomainBundle\Repository\App\AddressLookupQuotaHelperRepositoryDoctrine;

/**
 * Class AddressLookupFactory
 * @package Tixi\App\AppBundle\Address
 */
class AddressLookupFactory extends ContainerAware {

    public static $localAddressServiceId = 'localaddressmysql';
    public static $localPoiServiceId = 'localpoimysql';
    public static $googleServiceId = 'google';

    /**
     * @param $serviceId
     * @return null|AddressLookupServiceGoogle|AddressLookupServiceLocalDoctrineMysql
     * @throws \Exception
     */
    public function get($serviceId) {
        $service = null;
        if ($serviceId === self::$localAddressServiceId) {
            $service = new AddressLookupServiceLocalDoctrineMysql();
        } elseif ($serviceId === self::$localPoiServiceId) {
            $service = new PoiLookupServiceLocalDoctrineMysql();
        }
        elseif ($serviceId === self::$googleServiceId) {
            $service = new AddressLookupServiceGoogle();
        } else {
            throw new \Exception('no service with servicid ' . $serviceId . ' found');
        }
        $service->setContainer($this->container);
        if ($service->hasLookupQuota()) {
            $service->setLookupQuotaHelper($this->getLookupQuotaHelper($serviceId));
        }
        return $service;
    }

    /**
     * @param $serviceId
     * @return AddressLookupQuotaHelper
     */
    protected function getLookupQuotaHelper($serviceId) {
        $entityManager = $this->container->get('entity_manager');
        /** @var AddressLookupQuotaHelperRepositoryDoctrine $quotaHelperRepository */
        $quotaHelperRepository = $this->container->get('addressquotahelper_repository');

        /** @var AddressLookupQuotaHelper $quotaHelper */
        $quotaHelper = $quotaHelperRepository->find($serviceId);
        if (null === $quotaHelper) {
            $quotaHelper = AddressLookupQuotaHelper::registerLookupQuotaHelper($serviceId);
            $quotaHelperRepository->store($quotaHelper);
            $entityManager->flush();
        }
        return $quotaHelper;
    }
} 