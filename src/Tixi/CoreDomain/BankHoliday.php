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
    protected $date;

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param $date
     * @return BankHoliday
     */
    public static function registerBankHoliday($name, $date) {
        $bankHoliday = new BankHoliday();
        $bankHoliday->setName($name);
        $bankHoliday->setDate($date);
        return $bankHoliday;
    }

    public function updateBankHolidayData($name = null, $date = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($date)) {
            $this->setDate($date);
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
     * @param mixed $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }
} 