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
 * Tixi\CoreDomain\Person
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\PersonRepositoryDoctrine")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"person" = "Person", "driver" = "Driver", "passenger" = "Passenger"})
 */
class Person {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="correspondence_address_id", referencedColumnName="id")
     */
    protected $correspondenceAddress;

    /**
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id")
     */
    protected $billingAddress;

    /**
     * @ORM\OneToMany(targetEntity="Absent", mappedBy="person")
     * @ORM\JoinColumn(name="absent_id", referencedColumnName="id")
     */
    protected $absents;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $telephone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $entryDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $birthday;

    /**
     * extraMinutes for Driver to drive, or Guest for going and leaving the vehicle
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $extraMinutes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    protected function __construct() {
        $this->absents = new ArrayCollection();
        $this->vehicleTypes = new ArrayCollection();
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @return Person
     */
    public function registerPerson($title, $firstname, $lastname, $telephone,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null) {

        $person = new Person();

        $person->setTitle($title);
        $person->setFirstname($firstname);
        $person->setLastname($lastname);
        $person->setTelephone($telephone);

        if(!is_null($email)) {$person->setEmail($email);}
        if(!is_null($entryDate)) {$person->setEntryDate($entryDate);}
        if(!is_null($birthday)) {$person->setBirthday($birthday);}
        if(!is_null($extraMinutes)) {$person->setExtraMinutes($extraMinutes);}
        if(!is_null($details)) {$person->setDetails($details);}

        $person->activate();

        return $person;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     */
    public function updateBasicData($title = null, $firstname = null, $lastname = null, $telephone = null,
                                    $email = null, $entryDate = null, $birthday = null,
                                    $extraMinutes = null, $details = null) {
        if(!is_null($title)) {$this->setTitle($title);}
        if(!is_null($firstname)) {$this->setFirstname($firstname);}
        if(!is_null($lastname)) {$this->setLastname($lastname);}
        if(!is_null($telephone)) {$this->setTelephone($telephone);}
        if(!is_null($email)) {$this->setEmail($email);}
        if(!is_null($entryDate)) {$this->setEntryDate($entryDate);}
        if(!is_null($birthday)) {$this->setBirthday($birthday);}
        if(!is_null($extraMinutes)) {$this->setExtraMinutes($extraMinutes);}
        if(!is_null($details)) {$this->setDetails($details);}
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param Address $address
     */
    public function assignCorrespondenceAddress(Address $address) {
        $this->correspondenceAddress = $address;
    }

    /**
     * @param Address $address
     */
    public function assignBillingAddress(Address $address) {
        $this->billingAddress = $address;
    }

    /**
     * Assigns Absent to Person and OneToMany $person in Absent
     * @param Absent $absent
     */
    public function assignAbsent(Absent $absent) {
        $absent->setPerson($this);
        $this->absents->add($absent);
    }

    /**
     * @return mixed
     */
    public function getAbsents() {
        return $this->absents;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $billingAddress
     */
    public function setBillingAddress($billingAddress) {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return mixed
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getBirthday() {
        return $this->birthday;
    }

    /**
     * @param mixed $compatibleVehicles
     */
    public function setCompatibleVehicles($compatibleVehicles) {
        $this->compatibleVehicles = $compatibleVehicles;
    }

    /**
     * @return mixed
     */
    public function getCompatibleVehicles() {
        return $this->compatibleVehicles;
    }

    /**
     * @param mixed $correspondenceAddress
     */
    public function setCorrespondenceAddress($correspondenceAddress) {
        $this->correspondenceAddress = $correspondenceAddress;
    }

    /**
     * @return mixed
     */
    public function getCorrespondenceAddress() {
        return $this->correspondenceAddress;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details) {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $entryDate
     */
    public function setEntryDate($entryDate) {
        $this->entryDate = $entryDate;
    }

    /**
     * @return mixed
     */
    public function getEntryDate() {
        return $this->entryDate;
    }

    /**
     * @param mixed $extraMinutes
     */
    public function setExtraMinutes($extraMinutes) {
        $this->extraMinutes = $extraMinutes;
    }

    /**
     * @return mixed
     */
    public function getExtraMinutes() {
        return $this->extraMinutes;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }


}
