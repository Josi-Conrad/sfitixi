<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tixi\CoreDomain\Passenger
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\PassengerRepositoryDoctrine")
 * @ORM\Table(name="passenger")
 */
class Passenger extends Person {
    /**
     * @ORM\ManyToOne(targetEntity="Handicap")
     * @ORM\JoinColumn(name="handicap", referencedColumnName="id")
     */
    protected $handicap;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isInWheelChair;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isOverweight;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $gotMonthlyBilling;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notice;

    /**
     * @ORM\OneToMany(targetEntity="Tixi\CoreDomain\Dispo\DrivingOrder", mappedBy="passenger")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;

    protected function __construct() {
        parent::__construct();
        $this->drivingOrders = new ArrayCollection();
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $gender
     * @param Address $address
     * @param Handicap $handicap
     * @param bool $isInWheelChair
     * @param bool $gotMonthlyBilling
     * @param bool $isOverWeight
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     * @return Passenger
     */
    public static function registerPassenger($title, $firstname, $lastname, $telephone, $gender, Address $address, Handicap $handicap,
                                             $isInWheelChair = false, $gotMonthlyBilling = false, $isOverWeight = false,
                                             $email = null, $entryDate = null, $birthday = null,
                                             $extraMinutes = null, $details = null, $notice = null) {
        $passenger = new Passenger();
        $passenger->setTitle($title);
        $passenger->setFirstname($firstname);
        $passenger->setLastname($lastname);
        $passenger->setTelephone($telephone);
        $passenger->setGender($gender);
        $passenger->setAddress($address);
        $passenger->setHandicap($handicap);
        $passenger->setIsInWheelChair($isInWheelChair);
        $passenger->setGotMonthlyBilling($gotMonthlyBilling);
        $passenger->setIsOverweight($isOverWeight);

        if (!empty($email)) {
            $passenger->setEmail($email);
        }
        if (!empty($entryDate)) {
            $passenger->setEntryDate($entryDate);
        }
        if (!empty($birthday)) {
            $passenger->setBirthday($birthday);
        }
        if (!empty($extraMinutes)) {
            $passenger->setExtraMinutes($extraMinutes);
        }
        if (!empty($details)) {
            $passenger->setDetails($details);
        }
        if (!empty($notice)) {
            $passenger->setNotice($notice);
        }

        $passenger->activate();

        return $passenger;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param null $gender
     * @param Address $address
     * @param Handicap $handicap
     * @param null $isInWheelChair
     * @param null $gotMonthlyBilling
     * @param null $isOverWeight
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     */
    public function updatePassengerBasicData($title = null, $firstname = null, $lastname = null, $telephone = null, $gender = null,
                                             Address $address, Handicap $handicap = null, $isInWheelChair = null, $gotMonthlyBilling = null,
                                             $isOverWeight = null, $email = null, $entryDate = null, $birthday = null,
                                             $extraMinutes = null, $details = null, $notice = null) {
        if (!empty($title)) {
            $this->setTitle($title);
        }
        if (!empty($firstname)) {
            $this->setFirstname($firstname);
        }
        if (!empty($lastname)) {
            $this->setLastname($lastname);
        }
        if (!empty($telephone)) {
            $this->setTelephone($telephone);
        }
        if (!empty($gender)) {
            $this->setGender($gender);
        }
        if (!empty($address)) {
            $this->setAddress($address);
        }
        if (!empty($handicap)) {
            $this->setHandicap($handicap);
        }
        if (!empty($isInWheelChair)) {
            $this->setIsInWheelChair($isInWheelChair);
        }
        if (!empty($gotMonthlyBilling)) {
            $this->setGotMonthlyBilling($gotMonthlyBilling);
        }
        if (!empty($isOverWeight)) {
            $this->setIsOverweight($isOverWeight);
        }
        if (!empty($email)) {
            $this->setEmail($email);
        }
        if (!empty($entryDate)) {
            $this->setEntryDate($entryDate);
        }
        if (!empty($birthday)) {
            $this->setBirthday($birthday);
        }
        if (!empty($extraMinutes)) {
            $this->setExtraMinutes($extraMinutes);
        }
        if (!empty($details)) {
            $this->setDetails($details);
        }
        if (!empty($notice)) {
            $this->setNotice($notice);
        }
    }

    public static function removePassenger(Passenger $passenger){
        $passenger->removePerson();
    }
    /**
     * @param mixed $gotMonthlyBilling
     */
    public function setGotMonthlyBilling($gotMonthlyBilling) {
        $this->gotMonthlyBilling = $gotMonthlyBilling;
    }

    /**
     * @return mixed
     */
    public function getGotMonthlyBilling() {
        return $this->gotMonthlyBilling;
    }

    /**
     * @param mixed $handicap
     */
    public function setHandicap($handicap) {
        $this->handicap = $handicap;
    }

    /**
     * @return Handicap
     */
    public function getHandicap() {
        return $this->handicap;
    }

    /**
     * @param mixed $isOverweight
     */
    public function setIsOverweight($isOverweight) {
        $this->isOverweight = $isOverweight;
    }

    /**
     * @return mixed
     */
    public function getIsOverweight() {
        return $this->isOverweight;
    }

    /**
     * @param mixed $isInWheelChair
     */
    public function setIsInWheelChair($isInWheelChair) {
        $this->isInWheelChair = $isInWheelChair;
    }

    /**
     * @return mixed
     */
    public function getIsInWheelChair() {
        return $this->isInWheelChair;
    }

    /**
     * @param mixed $notice
     */
    public function setNotice($notice) {
        $this->notice = $notice;
    }

    /**
     * @return mixed
     */
    public function getNotice() {
        return $this->notice;
    }


}
