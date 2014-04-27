<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 21:30
 */

namespace Tixi\App\AppBundle\Address;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\CoreDomain\App\AddressLookupQuotaHelper;

abstract class AddressLookupService extends ContainerAware{

    protected $lookupLimit = 5;
    /** @var  AddressLookupQuotaHelper */
    protected $lookupQuotaHelper;

    public function lookup($lookupStr) {
        if($this->hasLookupQuota()) {
            if(!$this->lookupQuotaHelper->hasCapacity($this->getMaxDailyQuota(),$this->getMaxMonthlyQuota())) {
                throw new AddressLookupQuotaExceededException();
            }
            $this->persistentlyCountLookup();
        }
        return $this->getAddressHandlingDTOs($lookupStr);
    }

    protected function persistentlyCountLookup() {
        $this->lookupQuotaHelper->countLookup();
        $this->getEntityManager()->flush();
    }

    protected function getMaxDailyQuota() {
        return PHP_INT_MAX;
    }

    protected function getMaxMonthlyQuota() {
        return PHP_INT_MAX;
    }

    abstract public function hasLookupQuota();

    abstract protected function getAddressHandlingDTOs($lookupStr);

    /**
     * @param \Tixi\CoreDomain\App\AddressLookupQuotaHelper $lookupVolumeCounter
     */
    public function setLookupQuotaHelper($lookupVolumeCounter)
    {
        $this->lookupQuotaHelper = $lookupVolumeCounter;
    }

    /**
     * @return int
     */
    public function getLookupLimit()
    {
        return $this->lookupLimit;
    }

    protected function getEntityManager() {
        return $this->container->get('entity_manager');
    }






} 