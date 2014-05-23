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
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Person
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\PersonRepositoryDoctrine")
 * @ORM\Table(name="person")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"person" = "Person", "driver" = "Driver", "passenger" = "Passenger"})
 */
class Person extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * old Id from data integration
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $pin;

    /**
     * @ORM\OneToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Absent", mappedBy="person")
     * @ORM\JoinColumn(name="absent_id", referencedColumnName="id")
     */
    protected $absents;

    /**
     * @ORM\ManyToMany(targetEntity="VehicleCategory")
     * @ORM\JoinTable(name="person_contradicts_vehiclecategory",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="vehicle_category_id", referencedColumnName="id")}
     *      )
     */
    protected $contradictVehicleCategories;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $gender;

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
    protected $fax;

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
    protected $correspondenceAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $billingAddress;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isBillingAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    /**
     * @param $gender
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param \Tixi\CoreDomain\Address $address
     * @param $title
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param bool $isBillingAddress
     * @param null $fax
     */
    protected function __construct($gender, $firstname, $lastname, $telephone, Address $address, $title = null,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                   $billingAddress = null, $isBillingAddress = true, $fax = null) {

        $this->absents = new ArrayCollection();
        $this->contradictVehicleCategories = new ArrayCollection();

        $this->setGender($gender);
        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setTelephone($telephone);
        $this->setAddress($address);
        $this->setTitle($title);
        $this->setEmail($email);
        $this->setEntryDate($entryDate);
        $this->setBirthday($birthday);
        $this->setExtraMinutes($extraMinutes);
        $this->setDetails($details);
        $this->setCorrespondenceAddress($correspondenceAddress);
        $this->setBillingAddress($billingAddress);
        $this->setIsBillingAddress($isBillingAddress);
        $this->setFax($fax);

        parent::__construct();
    }

    /**
     * @param $gender
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $address
     * @param $title
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param null $fax
     * @return Person
     */
    public static function registerPerson($gender, $firstname, $lastname, $telephone, $address, $title = null,
                                          $email = null, $entryDate = null, $birthday = null,
                                          $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                          $billingAddress = null, $fax = null) {

        $person = new Person($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details, $correspondenceAddress, $billingAddress, $fax);

        return $person;
    }

    /**
     * @param null $gender
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param null $address
     * @param null $title
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @param null $correspondenceAddress
     * @param null $billingAddress
     * @param bool $isBillingAddress
     * @param null $fax
     */
    public function updatePersonData($gender = null, $firstname = null, $lastname = null, $telephone = null,
                                     $address = null, $title = null, $email = null, $entryDate = null, $birthday = null,
                                     $extraMinutes = null, $details = null, $correspondenceAddress = null,
                                     $billingAddress = null, $isBillingAddress = true, $fax = null) {

        if (!empty($gender)) {
            $this->setGender($gender);
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
        $this->setTitle($title);
        $this->setEmail($email);
        $this->setEntryDate($entryDate);
        $this->setBirthday($birthday);
        $this->setExtraMinutes($extraMinutes);
        $this->setDetails($details);
        $this->setCorrespondenceAddress($correspondenceAddress);
        $this->setBillingAddress($billingAddress);
        $this->setIsBillingAddress($isBillingAddress);
        $this->setFax($fax);

        $this->updateModifiedDate();
    }

    public function removePerson() {
        foreach ($this->getAbsents() as $a) {
            $this->removeAbsent($a);
        }
    }

    /**
     * Assigns Absent to Person and OneToMany $person in Absent
     * @param Absent $absent
     */
    public function assignAbsent(Absent $absent) {
        $this->absents->add($absent);
        $absent->setPerson($this);
    }

    /**
     * @param Absent $absent
     */
    public function removeAbsent(Absent $absent) {
        $this->absents->removeElement($absent);
        $absent->setPerson(null);
    }

    /**
     * @param VehicleCategory $vehicleCategory
     */
    public function assignContradictVehicleCategory($vehicleCategory) {
        $this->contradictVehicleCategories->add($vehicleCategory);
    }

    /**
     * @return mixed
     */
    public function getContradictVehicleCategories() {
        return $this->contradictVehicleCategories;
    }

    /**
     * @param $contradictVehicleCategories
     */
    public function setContradictVehicleCategories($contradictVehicleCategories) {
        $this->contradictVehicleCategories = $contradictVehicleCategories;
    }

    /**
     * @return Absent[]
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

    /**
     * @param mixed $gender
     */
    public function setGender($gender) {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * @param mixed $isBillingAddress
     */
    public function setIsBillingAddress($isBillingAddress) {
        $this->isBillingAddress = $isBillingAddress;
    }

    /**
     * @return mixed
     */
    public function getIsBillingAddress() {
        return $this->isBillingAddress;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * @return string
     */
    public function getGenderAsString() {
        return self::constructGenderString($this->getGender());
    }

    /**
     * @param $gender
     * @return string
     */
    public static function constructGenderString($gender) {
        $genderString = '';
        if ($gender == 'm') {
            $genderString = 'person.gender.male';
        } else {
            $genderString = 'person.gender.female';
        }
        return $genderString;
    }

}
