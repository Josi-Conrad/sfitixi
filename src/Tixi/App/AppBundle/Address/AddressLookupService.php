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

/**
 * Class AddressLookupService
 * @package Tixi\App\AppBundle\Address
 */
abstract class AddressLookupService extends ContainerAware {

    protected $lookupLimit = 5;
    /** @var  AddressLookupQuotaHelper */
    protected $lookupQuotaHelper;

    /**
     * @param $lookupStr
     * @return mixed
     * @throws AddressLookupQuotaExceededException
     */
    public function lookup($lookupStr) {
        if ($this->hasLookupQuota()) {
            if (!$this->lookupQuotaHelper->hasCapacity($this->getMaxDailyQuota(), $this->getMaxMonthlyQuota())) {
                throw new AddressLookupQuotaExceededException();
            }
            $this->persistentlyCountLookup();
        }
        return $this->getAddressHandlingDTOs($lookupStr);
    }

    /**
     * @param $lookupStr
     * @return mixed
     * @throws AddressLookupQuotaExceededException
     */
    public function lookupSingleAddress($lookupStr) {
        if ($this->hasLookupQuota()) {
            if (!$this->lookupQuotaHelper->hasCapacity($this->getMaxDailyQuota(), $this->getMaxMonthlyQuota())) {
                throw new AddressLookupQuotaExceededException();
            }
            $this->persistentlyCountLookup();
        }
        return $this->getSingleAddressHandleDTO($lookupStr);
    }

    /**
     * count the lookup and persist to database
     */
    protected function persistentlyCountLookup() {
        $this->lookupQuotaHelper->countLookup();
        $this->getEntityManager()->flush();
    }

    /**
     * @return int
     */
    protected function getMaxDailyQuota() {
        return PHP_INT_MAX;
    }

    /**
     * @return int
     */
    protected function getMaxMonthlyQuota() {
        return PHP_INT_MAX;
    }

    /**
     * @return mixed
     */
    abstract public function hasLookupQuota();

    /**
     * @param $lookupStr
     * @return mixed
     */
    abstract protected function getAddressHandlingDTOs($lookupStr);

    /**
     * @param $lookupStr
     * @return mixed
     */
    abstract protected function getSingleAddressHandleDTO($lookupStr);

    /**
     * @param \Tixi\CoreDomain\App\AddressLookupQuotaHelper $lookupVolumeCounter
     */
    public function setLookupQuotaHelper($lookupVolumeCounter) {
        $this->lookupQuotaHelper = $lookupVolumeCounter;
    }

    /**
     * @return int
     */
    public function getLookupLimit() {
        return $this->lookupLimit;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager() {
        return $this->container->get('entity_manager');
    }


}