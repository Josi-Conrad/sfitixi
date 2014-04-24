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
     * @ORM\ManyToMany(targetEntity="Handicap")
     * @ORM\JoinTable(name="passenger_to_handicap",
     *      joinColumns={@ORM\JoinColumn(name="passenger_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="handicap_id", referencedColumnName="id")})
     */
    protected $handicaps;
    /**
     * @ORM\ManyToMany(targetEntity="Insurance")
     * @ORM\JoinTable(name="passenger_to_insurance",
     *      joinColumns={@ORM\JoinColumn(name="passenger_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="insurance_id", referencedColumnName="id")})
     */
    protected $insurances;

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

    protected function __construct($gender, $firstname, $lastname, $telephone, $address, $title = null,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                   $billingAddress = null) {

        $this->handicaps = new ArrayCollection();
        $this->insurances = new ArrayCollection();
        $this->drivingOrders = new ArrayCollection();

        parent::__construct($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress);
    }

    /**
     * @param $gender
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param Address $address
     * @param $title
     * @param bool $isInWheelChair
     * @param bool $gotMonthlyBilling
     * @param bool $isOverWeight
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @internal param \Tixi\CoreDomain\Handicap $handicap
     * @return Passenger
     */
    public static function registerPassenger($gender, $firstname, $lastname, $telephone, Address $address, $title = null,
                                             $isInWheelChair = false, $gotMonthlyBilling = false, $isOverWeight = false,
                                             $email = null, $entryDate = null, $birthday = null,
                                             $extraMinutes = null, $details = null, $notice = null, $correspondenceAddress = null,
                                             $billingAddress = null) {

        $passenger = new Passenger($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress
        );

        $passenger->setIsInWheelChair($isInWheelChair);
        $passenger->setGotMonthlyBilling($gotMonthlyBilling);
        $passenger->setIsOverweight($isOverWeight);
        $passenger->setNotice($notice);

        return $passenger;
    }

    /**
     * @param null $gender
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param Address $address
     * @param null $title
     * @param bool|null $isInWheelChair
     * @param bool|null $gotMonthlyBilling
     * @param bool|null $isOverWeight
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @internal param \Tixi\CoreDomain\Handicap $handicap
     */
    public function updatePassengerData($gender = null, $firstname = null, $lastname = null, $telephone = null,
                                        Address $address, $title = null, $isInWheelChair = false, $gotMonthlyBilling = null,
                                        $isOverWeight = null, $email = null, $entryDate = null, $birthday = null,
                                        $extraMinutes = null, $details = null, $notice = null, $correspondenceAddress = null,
                                        $billingAddress = null) {

        parent::updatePersonData(
            $gender, $firstname, $lastname, $telephone, $address, $title, $email, $entryDate, $birthday,
            $extraMinutes, $details, $correspondenceAddress, $billingAddress
        );

        $this->setIsInWheelChair($isInWheelChair);
        $this->setGotMonthlyBilling($gotMonthlyBilling);
        $this->setIsOverweight($isOverWeight);
        $this->setNotice($notice);
    }

    /**
     * @param Passenger $passenger
     */
    public static function removePassenger(Passenger $passenger) {
        $passenger->removePerson();
    }

    /**
     * @param Handicap $handicap
     */
    public function assignHandicap(Handicap $handicap) {
        $this->handicaps->add($handicap);
    }

    /**
     * @param Handicap $handicap
     */
    public function removeHandicap(Handicap $handicap) {
        $this->handicaps->removeElement($handicap);
    }

    /**
     * @param Insurance $insurance
     */
    public function assignInsurance(Insurance $insurance) {
        $this->insurances->add($insurance);
    }

    /**
     * @param Insurance $insurance
     */
    public function removeInsurance(Insurance $insurance) {
        $this->insurances->removeElement($insurance);
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

    /**
     * @return mixed
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @return mixed
     */
    public function getDrivingOrders() {
        return $this->drivingOrders;
    }

    /**
     * @return mixed
     */
    public function getHandicaps() {
        return $this->handicaps;
    }

    /**
     * @return mixed
     */
    public function getInsurances() {
        return $this->insurances;
    }

    /**
     * @return mixed
     */
    public function getModifyDate() {
        return $this->modifyDate;
    }

    /**
     * @param mixed $handicaps
     */
    public function setHandicaps($handicaps) {
        $this->handicaps = $handicaps;
    }

    /**
     * @param mixed $insurances
     */
    public function setInsurances($insurances) {
        $this->insurances = $insurances;
    }
}
