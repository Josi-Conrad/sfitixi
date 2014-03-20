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
     * @ORM\OneToMany(targetEntity="Contradict", mappedBy="passenger")
     * @ORM\JoinColumn(name="contradict_id", referencedColumnName="id")
     **/
    protected $contradicts;

    /**
     * @ORM\ManyToOne(targetEntity="Handicap")
     * @ORM\JoinColumn(name="handicap", referencedColumnName="name")
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

    protected function __construct() {
        parent::__construct();
        $this->contradicts = new ArrayCollection();
    }


    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param Address $address
     * @param Handicap $handicap
     * @param $isInWheelChair
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
    public static function registerPassenger($title, $firstname, $lastname, $telephone, Address $address, Handicap $handicap,
                                             $isInWheelChair, $gotMonthlyBilling = false, $isOverWeight = false,
                                             $email = null, $entryDate = null, $birthday = null,
                                             $extraMinutes = null, $details = null, $notice = null) {
        $passenger = new Passenger();

        $passenger->setTitle($title);
        $passenger->setFirstname($firstname);
        $passenger->setLastname($lastname);
        $passenger->setTelephone($telephone);
        $passenger->setAddress($address);
        $passenger->setHandicap($handicap);
        $passenger->setIsInWheelChair($isInWheelChair);
        $passenger->setGotMonthlyBilling($gotMonthlyBilling);
        $passenger->setIsOverweight($isOverWeight);

        if(!is_null($notice)) {$passenger->setNotice($notice);}
        if(!is_null($email)) {$passenger->setEmail($email);}
        if(!is_null($entryDate)) {$passenger->setEntryDate($entryDate);}
        if(!is_null($birthday)) {$passenger->setBirthday($birthday);}
        if(!is_null($extraMinutes)) {$passenger->setExtraMinutes($extraMinutes);}
        if(!is_null($details)) {$passenger->setDetails($details);}

        $passenger->activate();

        return $passenger;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
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
    public function updatePassengerBasicData($title = null, $firstname = null, $lastname = null, $telephone = null,
                                    Address $address, Handicap $handicap = null, $isInWheelChair = null, $gotMonthlyBilling = null,
                                    $isOverWeight = null, $email = null, $entryDate = null, $birthday = null,
                                    $extraMinutes = null, $details = null, $notice = null) {
        if(!is_null($title)) {$this->setTitle($title);}
        if(!is_null($firstname)) {$this->setFirstname($firstname);}
        if(!is_null($lastname)) {$this->setLastname($lastname);}
        if(!is_null($telephone)) {$this->setTelephone($telephone);}
        if(!is_null($address)) {$this->setAddress($address);}
        if(!is_null($handicap)) {$this->setHandicap($handicap);}
        if(!is_null($isInWheelChair)) {$this->setIsInWheelChair($isInWheelChair);}
        if(!is_null($gotMonthlyBilling)) {$this->setGotMonthlyBilling($gotMonthlyBilling);}
        if(!is_null($isOverWeight)) {$this->setIsOverweight($isOverWeight);}
        if(!is_null($email)) {$this->setEmail($email);}
        if(!is_null($entryDate)) {$this->setEntryDate($entryDate);}
        if(!is_null($birthday)) {$this->setBirthday($birthday);}
        if(!is_null($extraMinutes)) {$this->setExtraMinutes($extraMinutes);}
        if(!is_null($details)) {$this->setDetails($details);}
        if(!is_null($notice)) {$this->setNotice($notice);}
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param Driver $driver
     * @param null $comment
     * @return Contradict
     */
    public function assignNewContradictWithDriver(Driver $driver, $comment = null) {
        $contradict = new Contradict($this, $driver, $comment);
        $this->assignContradict($contradict);
        $driver->assignContradict($contradict);
        return $contradict;
    }

    /**
     * @param Contradict $contradict
     */
    public function assignContradict(Contradict $contradict) {
        $this->contradicts->add($contradict);
    }

    /**
     * call $em->flush() after this operation
     * @param Contradict $contradict
     */
    public function removeContradict(Contradict $contradict) {
        $contradict->unlinkAssociations();
    }

    /**
     * @return ArrayCollection
     */
    public function getContradicts() {
        return $this->contradicts;
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
