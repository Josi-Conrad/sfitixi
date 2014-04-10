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
 * @ORM\Table(name="person")
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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $creationDate;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $modifyDate;

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $address
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     */
    protected function __construct($title, $firstname, $lastname, $telephone, $address,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                   $billingAddress = null) {

        $this->creationDate = new \DateTime("now");
        $this->absents = new ArrayCollection();

        $this->setTitle($title);
        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setTelephone($telephone);
        $this->setAddress($address);
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
        if (!empty($correspondenceAddress)) {
            $this->setCorrespondenceAddress($correspondenceAddress);
        }
        if (!empty($billingAddress)) {
            $this->setBillingAddress($billingAddress);
        }
        $this->activate();
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $address
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @return Person
     */
    public static function registerPerson($title, $firstname, $lastname, $telephone, $address,
                                          $email = null, $entryDate = null, $birthday = null,
                                          $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                          $billingAddress = null) {

        $person = new Person($title, $firstname, $lastname, $telephone, $address,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress);

        return $person;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param null $address
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     */
    public function updatePersonBasicData($title = null, $firstname = null, $lastname = null, $telephone = null,
                                          $address = null, $email = null, $entryDate = null, $birthday = null,
                                          $extraMinutes = null, $details = null, $correspondenceAddress = null, $billingAddress = null) {

        $this->modifyDate = new \DateTime("now");

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
        if (!empty($address)) {
            $this->setAddress($address);
        }
        $this->setEmail($email);
        $this->setEntryDate($entryDate);
        $this->setBirthday($birthday);
        $this->setExtraMinutes($extraMinutes);
        $this->setDetails($details);
        $this->setCorrespondenceAddress($correspondenceAddress);
        $this->setBillingAddress($billingAddress);
    }

    public function removePerson() {
        foreach ($this->getAbsents() as $a) {
            $this->removeAbsent($a);
        }
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }


    /**
     * Assigns Absent to Person and OneToMany $person in Absent
     * @param Absent $absent
     */
    public function assignAbsent(Absent $absent) {
        $this->absents->add($absent);
        $absent->setPerson($this);
    }

    public function removeAbsent(Absent $absent) {
        $this->absents->removeElement($absent);
        $absent->setPerson(null);
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
     * @return Address
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
     * @return \DateTime
     */
    public function getEntryDate() {
        return $this->entryDate;
    }


    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday() {
        return $this->birthday;
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
     * @return Boolean
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

    public function getNameString() {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getNameStringWithID() {
        return $this->firstname . ' ' . $this->lastname . ' (ID: ' . $this->id . ')';
    }

}
