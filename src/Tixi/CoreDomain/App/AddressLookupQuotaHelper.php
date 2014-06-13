<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 21:37
 */

namespace Tixi\CoreDomain\App;

use Doctrine\ORM\Mapping as ORM;
use Tixi\ApiBundle\Helper\DateTimeService;

/**
 * Class AddressLookupConfig
 * @package Tixi\CoreDomain\App
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\App\AddressLookupQuotaHelperRepositoryDoctrine")
 * @ORM\Table(name="addresslookupquotahelper")
 */
class AddressLookupQuotaHelper {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=20)
     */
    protected $id;
    /**
     * @ORM\Column(type="integer")
     */
    protected $currentDailyLookups;
    /**
     * @ORM\Column(type="integer")
     */
    protected $currentMonthlyLookups;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $lastLookupDateTime;

    protected function __construct() {

    }

    /**
     * @param $id
     * @return AddressLookupQuotaHelper
     */
    public static function registerLookupQuotaHelper($id) {
        $volume = new AddressLookupQuotaHelper();
        $volume->setId($id);
        $volume->setLastLookupDateTime(DateTimeService::getUTCnow());
        $volume->setCurrentDailyLookups(0);
        $volume->setCurrentMonthlyLookups(0);
        return $volume;
    }

    /**
     * @param $maxDailyQuota
     * @param $maxMonthlyQuota
     * @return bool
     */
    public function hasCapacity($maxDailyQuota, $maxMonthlyQuota) {
        return ((($this->getCurrentDailyLookups()+1)<=$maxDailyQuota) &&
            (($this->getCurrentMonthlyLookups()+1)<=$maxMonthlyQuota));
    }

    /**
     * counts each lookup, necessary for several API's
     */
    public function countLookup() {
        $now = DateTimeService::getUTCnow();
        if($this->isNewLookupMonth($now)) {
            $this->resetMonthlyStatus();
            $this->resetDailyStatus();
        }elseif($this->isNewLookupDay($now)) {
            $this->resetDailyStatus();
        }
        $this->currentDailyLookups++;
        $this->currentMonthlyLookups++;
    }

    /**
     * @param $now
     * @return bool
     */
    protected function isNewLookupDay($now) {
        $toReturn = false;
        if(DateTimeService::getYear($now)!==DateTimeService::getYear($this->lastLookupDateTime) ||
                DateTimeService::getMonth($now)!==DateTimeService::getMonth($this->lastLookupDateTime) ||
                    DateTimeService::getDayOfMonth($now)!==DateTimeService::getDayOfMonth($this->lastLookupDateTime)) {
            $toReturn = true;
        }
        return $toReturn;
    }

    /**
     * @param $now
     * @return bool
     */
    protected function isNewLookupMonth($now) {
        $toReturn = false;
        if(DateTimeService::getYear($now)!==DateTimeService::getYear($this->lastLookupDateTime) ||
            DateTimeService::getMonth($now)!==DateTimeService::getMonth($this->lastLookupDateTime)) {
            $toReturn = true;
        }
        return $toReturn;
    }

    /**
     * resets daily lookup count
     */
    protected function resetDailyStatus() {
        $this->setCurrentDailyLookups(0);
    }

    /**
     * resets monthly lookup
     */
    protected function resetMonthlyStatus() {
        $this->setCurrentMonthlyLookups(0);
    }

    /**
     * @param mixed $currentDailyStatus
     */
    public function setCurrentDailyLookups($currentDailyStatus)
    {
        $this->currentDailyLookups = $currentDailyStatus;
    }

    /**
     * @return mixed
     */
    public function getCurrentDailyLookups()
    {
        return $this->currentDailyLookups;
    }

    /**
     * @param mixed $currentMonthlyStatus
     */
    public function setCurrentMonthlyLookups($currentMonthlyStatus)
    {
        $this->currentMonthlyLookups = $currentMonthlyStatus;
    }

    /**
     * @return mixed
     */
    public function getCurrentMonthlyLookups()
    {
        return $this->currentMonthlyLookups;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $lastLookupDateTime
     */
    public function setLastLookupDateTime($lastLookupDateTime)
    {
        $this->lastLookupDateTime = $lastLookupDateTime;
    }

    /**
     * @return mixed
     */
    public function getLastLookupDateTime()
    {
        return $this->lastLookupDateTime;
    }


} 