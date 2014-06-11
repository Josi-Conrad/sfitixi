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
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;

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
    protected $hasMonthlyBilling;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notice;

    /**
     * @ORM\OneToMany(targetEntity="Tixi\CoreDomain\Dispo\DrivingOrder", mappedBy="passenger")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;

    /**
     * @ORM\OneToMany(targetEntity="Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan", mappedBy="passenger")
     * @ORM\JoinColumn(name="repeated_driving_order_plan_id", referencedColumnName="id")
     */
    protected $repeatedDrivingOrderPlans;

    /**
     * @param $gender
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $address
     * @param null $title
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param bool $isBillingAddress
     */
    protected function __construct($gender, $firstname, $lastname, $telephone, $address, $title = null,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                   $billingAddress = null, $isBillingAddress = true) {

        $this->handicaps = new ArrayCollection();
        $this->insurances = new ArrayCollection();
        $this->drivingOrders = new ArrayCollection();

        parent::__construct($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress, $isBillingAddress);
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
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param bool $isBillingAddress
     * @return Passenger
     */
    public static function registerPassenger($gender, $firstname, $lastname, $telephone, Address $address, $title = null,
                                             $isInWheelChair = false, $gotMonthlyBilling = false,
                                             $email = null, $entryDate = null, $birthday = null,
                                             $extraMinutes = null, $details = null, $notice = null, $correspondenceAddress = null,
                                             $billingAddress = null, $isBillingAddress = true) {

        $passenger = new Passenger($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress, $isBillingAddress
        );

        $passenger->setIsInWheelChair($isInWheelChair);
        $passenger->setHasMonthlyBilling($gotMonthlyBilling);
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
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $notice
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param bool $isBillingAddress
     */
    public function updatePassengerData($gender = null, $firstname = null, $lastname = null, $telephone = null,
                                        Address $address, $title = null, $isInWheelChair = false, $gotMonthlyBilling = null,
                                        $email = null, $entryDate = null, $birthday = null,
                                        $extraMinutes = null, $details = null, $notice = null, $correspondenceAddress = null,
                                        $billingAddress = null, $isBillingAddress = true) {

        parent::updatePersonData(
            $gender, $firstname, $lastname, $telephone, $address, $title, $email, $entryDate, $birthday,
            $extraMinutes, $details, $correspondenceAddress, $billingAddress, $isBillingAddress
        );

        $this->setIsInWheelChair($isInWheelChair);
        $this->setHasMonthlyBilling($gotMonthlyBilling);
        $this->setNotice($notice);
    }

    /**
     * @param Passenger $passenger
     */
    public static function removePassenger(Passenger $passenger) {
        $passenger->removePerson();
    }

    /**
     * @param DrivingOrder $drivingOrder
     */
    public function assignDrivingOrder(DrivingOrder $drivingOrder) {
        $this->getDrivingOrders()->add($drivingOrder);
    }

    public function removeDrivingOrder(DrivingOrder $drivingOrder) {
        $this->drivingOrders->removeElement($drivingOrder);
    }

    /**
     * @param RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan
     */
    public function assignRepeatedDrivingOrderPlan(RepeatedDrivingOrderPlan $repeatedDrivingOrderPlan) {
        $this->getRepeatedDrivingOrderPlans()->add($repeatedDrivingOrderPlan);
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
    public function setHasMonthlyBilling($gotMonthlyBilling) {
        $this->hasMonthlyBilling = $gotMonthlyBilling;
    }

    /**
     * @return mixed
     */
    public function getHasMonthlyBilling() {
        return $this->hasMonthlyBilling;
    }

    /**
     * @param mixed $isInWheelChair
     */
    public function setIsInWheelChair($isInWheelChair) {
        $this->isInWheelChair = $isInWheelChair;
    }

    /**
     * @return bool
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
     * @return ArrayCollection
     */
    public function getDrivingOrders() {
        return $this->drivingOrders;
    }

    /**
     * @return ArrayCollection
     */
    public function getRepeatedDrivingOrderPlans() {
        return $this->repeatedDrivingOrderPlans;
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
     * @param  $handicaps
     */
    public function setHandicaps($handicaps) {
        $this->handicaps = $handicaps;
    }

    /**
     * @param  $insurances
     */
    public function setInsurances($insurances) {
        $this->insurances = $insurances;
    }

    /**
     * @return string
     */
    public function getIsInWheelChairAsString() {
        return self::constructIsInWheelChairString($this->getIsInWheelChair());
    }

    /**
     * @param $isInWheelChair
     * @return string
     */
    public static function constructIsInWheelChairString($isInWheelChair) {
        return $isInWheelChair ? 'passenger.isinwheelchair.yes' : 'passenger.isinwheelchair.no';
    }

    /**
     * @return string
     */
    public function getMonthlyBillingAsString() {
        return self::constructMonthlyBillingString($this->getHasMonthlyBilling());
    }

    /**
     * @param $monthlyBilling
     * @return string
     */
    public static function constructMonthlyBillingString($monthlyBilling) {
        return $monthlyBilling ? 'passenger.monthlybilling.yes' : 'passenger.monthlybilling.no';
    }

    /**
     * @return string
     */
    public function getInsurancesAsString() {
        return self::constructInsurancesString($this->getInsurances());
    }

    /**
     * @param $insurances
     * @return string
     */
    public static function constructInsurancesString($insurances) {
        $string = '';
        foreach ($insurances as $key => $insurance) {
            if ($key !== 0) {
                $string .= ', ';
            }
            $string .= $insurance->getName();
        }
        return $string;
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return bool
     */
    public function isCompatibleWithVehicleCategory(VehicleCategory $vehicleCategory) {
        if($this->isInWheelChair){
            if($vehicleCategory->getAmountOfWheelChairs() < 1){
                return false;
            }
        }
        foreach ($this->contradictVehicleCategories as $contradict) {
            if ($vehicleCategory->getId() === $contradict->getId()) {
                return false;
            }
        }
        return true;
    }
}
