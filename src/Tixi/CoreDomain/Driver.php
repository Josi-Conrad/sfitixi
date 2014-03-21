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
 * Tixi\CoreDomain\Driver
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine")
 * @ORM\Table(name="driver")
 */
class Driver extends Person {
    /**
     * @ORM\OneToMany(targetEntity="Contradict", mappedBy="driver")
     * @ORM\JoinColumn(name="contradict_id", referencedColumnName="id")
     **/
    protected $contradicts;

    /**
     * @ORM\ManyToOne(targetEntity="DriverCategory")
     * @ORM\JoinColumn(name="driver_category", referencedColumnName="name")
     */
    protected $driverCategory;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="driver")
     * @ORM\JoinColumn(name="supervised_vehicle_id", referencedColumnName="id")
     */
    protected $supervisedVehicles;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $licenceNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $wheelChairAttendance;

    protected function __construct() {
        parent::__construct();
        $this->supervisedVehicles = new ArrayCollection();
        $this->contradicts = new ArrayCollection();
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param Address $address
     * @param $licenceNumber
     * @param DriverCategory $driverCategory
     * @param $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @return Driver
     */
    public static function registerDriver($title, $firstname, $lastname, $telephone, $licenceNumber, Address $address,
                                   DriverCategory $driverCategory, $wheelChairAttendance = true, $email = null,
                                   $entryDate = null, $birthday = null, $extraMinutes = null, $details = null) {
        $driver = new Driver();

        $driver->setTitle($title);
        $driver->setFirstname($firstname);
        $driver->setLastname($lastname);
        $driver->setTelephone($telephone);
        $driver->setAddress($address);
        $driver->setLicenceNumber($licenceNumber);
        $driver->setDriverCategory($driverCategory);
        $driver->setWheelChairAttendance($wheelChairAttendance);

        if(!empty($email)) {$driver->setEmail($email);}
        if(!empty($entryDate)) {$driver->setEntryDate($entryDate);}
        if(!empty($birthday)) {$driver->setBirthday($birthday);}
        if(!empty($extraMinutes)) {$driver->setExtraMinutes($extraMinutes);}
        if(!empty($details)) {$driver->setDetails($details);}

        $driver->activate();

        return $driver;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param Address $address
     * @param null $licenceNumber
     * @param DriverCategory $driverCategory
     * @param null $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     */
    public function updateDriverBasicData($title = null, $firstname = null, $lastname = null, $telephone = null,
                                    Address $address = null, $licenceNumber = null, DriverCategory $driverCategory = null,
                                    $wheelChairAttendance = null, $email = null, $entryDate = null, $birthday = null,
                                    $extraMinutes = null, $details = null) {
        if(!empty($title)) {$this->setTitle($title);}
        if(!empty($firstname)) {$this->setFirstname($firstname);}
        if(!empty($lastname)) {$this->setLastname($lastname);}
        if(!empty($telephone)) {$this->setTelephone($telephone);}
        if(!empty($licenceNumber)) {$this->setLicenceNumber($licenceNumber);}
        if(!empty($driverCategory)) {$this->setDriverCategory($driverCategory);}
        if(!empty($wheelChairAttendance)) {$this->setWheelChairAttendance($wheelChairAttendance);}
        if(!empty($email)) {$this->setEmail($email);}
        if(!empty($entryDate)) {$this->setEntryDate($entryDate);}
        if(!empty($birthday)) {$this->setBirthday($birthday);}
        if(!empty($extraMinutes)) {$this->setExtraMinutes($extraMinutes);}
        if(!empty($details)) {$this->setDetails($details);}
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param Passenger $passenger
     * @param null $comment
     * @return Contradict
     */
    public function assignNewContradictWithPassenger(Passenger $passenger, $comment = null) {
        $contradict = new Contradict($this, $passenger, $comment);
        $this->assignContradict($contradict);
        $passenger->assignContradict($contradict);
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
        $contradict->getPassenger()->removeContradict($contradict);
        $this->contradicts->removeElement($contradict);
        $contradict->unlinkAssociations();
    }

    /**
     * @param Vehicle $vehicle
     */
    public function superviseVehicle(Vehicle $vehicle) {
        $this->assignSupervisedVehicle($vehicle);
        $vehicle->assignSupervisor($this);
    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignSupervisedVehicle(Vehicle $vehicle) {
        $this->supervisedVehicles->add($vehicle);
    }

    /**
     * @param Vehicle $vehicle
     */
    public function removeSupervisedVehicle(Vehicle $vehicle){
        $this->supervisedVehicles->removeElement($vehicle);
        $vehicle->assignSupervisor(null);
    }
    /**
     * @param mixed $advisedVehicles
     */
    public function setAdvisedVehicles($advisedVehicles) {
        $this->advisedVehicles = $advisedVehicles;
    }

    /**
     * @return mixed
     */
    public function getAdvisedVehicles() {
        return $this->advisedVehicles;
    }

    /**
     * @param mixed $contradicts
     */
    public function setContradicts($contradicts) {
        $this->contradicts = $contradicts;
    }

    /**
     * @return ArrayCollection
     */
    public function getContradicts() {
        return $this->contradicts;
    }

    /**
     * @param mixed $driverCategory
     */
    public function setDriverCategory($driverCategory) {
        $this->driverCategory = $driverCategory;
    }

    /**
     * @return mixed
     */
    public function getDriverCategory() {
        return $this->driverCategory;
    }

    /**
     * @param mixed $licenceNumber
     */
    public function setLicenceNumber($licenceNumber) {
        $this->licenceNumber = $licenceNumber;
    }

    /**
     * @return mixed
     */
    public function getLicenceNumber() {
        return $this->licenceNumber;
    }

    /**
     * @param mixed $operationWish
     */
    public function setOperationWish($operationWish) {
        $this->operationWish = $operationWish;
    }

    /**
     * @return mixed
     */
    public function getOperationWish() {
        return $this->operationWish;
    }

    /**
     * @param mixed $wheelChairAttendance
     */
    public function setWheelChairAttendance($wheelChairAttendance) {
        $this->wheelChairAttendance = $wheelChairAttendance;
    }

    /**
     * @return mixed
     */
    public function getWheelChairAttendance() {
        return $this->wheelChairAttendance;
    }


}
