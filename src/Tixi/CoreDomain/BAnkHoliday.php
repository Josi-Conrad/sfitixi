<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 09:38
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\BankHolidayRepositoryDoctrine")
 * @ORM\Table(name="bankholiday")
 */
class BankHoliday extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;
    /**
     * @ORM\Column(type="date")
     */
    protected $startDate;
    /**
     * @ORM\Column(type="date")
     */
    protected $endDate;

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param $startDate
     * @param $endDate
     * @return BankHoliday
     */
    public static function registerBankHoliday($name, $startDate, $endDate) {
        $bankHoliday = new BankHoliday();
        $bankHoliday->setName($name);
        $bankHoliday->setStartDate($startDate);
        $bankHoliday->setEndDate($endDate);
        return $bankHoliday;
    }

    public function updateBankHolidayData($name = null, $startDate = null, $endDate = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($startDate)) {
            $this->setStartDate($startDate);
        }
        if (!empty($endDate)) {
            $this->setEndDate($endDate);
        }
    }


    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }
} 