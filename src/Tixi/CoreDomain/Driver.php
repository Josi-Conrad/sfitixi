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
     * @ORM\ManyToOne(targetEntity="DriverCategory")
     * @ORM\JoinColumn(name="driver_category", referencedColumnName="id")
     */
    protected $driverCategory;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="supervisor")
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
    }

    /**
     * @param $title
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param $gender
     * @param $licenceNumber
     * @param Address $address
     * @param DriverCategory $driverCategory
     * @param bool $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @return Driver
     */
    public static function registerDriver($title, $firstname, $lastname, $telephone, $gender, $licenceNumber, Address $address,
                                          DriverCategory $driverCategory, $wheelChairAttendance = true, $email = null,
                                          $entryDate = null, $birthday = null, $extraMinutes = null, $details = null) {
        $driver = new Driver();

        $driver->setTitle($title);
        $driver->setFirstname($firstname);
        $driver->setLastname($lastname);
        $driver->setTelephone($telephone);
        $driver->setLicenceNumber($licenceNumber);
        $driver->setAddress($address);
        $driver->setDriverCategory($driverCategory);
        $driver->setWheelChairAttendance($wheelChairAttendance);
        $driver->setGender($gender);

        if (!empty($email)) {
            $driver->setEmail($email);
        }
        if (!empty($entryDate)) {
            $driver->setEntryDate($entryDate);
        }
        if (!empty($birthday)) {
            $driver->setBirthday($birthday);
        }
        if (!empty($extraMinutes)) {
            $driver->setExtraMinutes($extraMinutes);
        }
        if (!empty($details)) {
            $driver->setDetails($details);
        }

        $driver->activate();

        return $driver;
    }

    /**
     * @param null $title
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param null $gender
     * @param null $licenceNumber
     * @param Address $address
     * @param DriverCategory $driverCategory
     * @param null $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     */
    public function updateDriverBasicData($title = null, $firstname = null, $lastname = null, $telephone = null, $gender = null,
                                          $licenceNumber = null, Address $address = null, DriverCategory $driverCategory = null,
                                          $wheelChairAttendance = null, $email = null, $entryDate = null, $birthday = null,
                                          $extraMinutes = null, $details = null) {
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
        if (!empty($licenceNumber)) {
            $this->setLicenceNumber($licenceNumber);
        }
        if (!empty($address)) {
            $this->setAddress($address);
        }
        if (!empty($driverCategory)) {
            $this->setDriverCategory($driverCategory);
        }
        if (!empty($wheelChairAttendance)) {
            $this->setWheelChairAttendance($wheelChairAttendance);
        }
        if (!empty($gender)) {
            $this->setGender($gender);
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
    }

    /**
     * Remove Driver, delete all Associations
     * @param Driver $driver
     */
    public static function removeDriver(Driver $driver) {
        $driver->removePerson();
        foreach ($driver->getSupervisedVehicles() as $v) {
            $driver->removeSupervisedVehicle($v);
        }
    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignSupervisedVehicle(Vehicle $vehicle) {
        $this->supervisedVehicles->add($vehicle);
        $vehicle->assignSupervisor($this);
    }

    /**
     * @param Vehicle $vehicle
     */
    public function removeSupervisedVehicle(Vehicle $vehicle) {
        $this->supervisedVehicles->removeElement($vehicle);
        $vehicle->removeSupervisor();
    }

    /**
     * @return Vehicle[]
     */
    public function getSupervisedVehicles() {
        return $this->supervisedVehicles;
    }

    /**
     * @param mixed $driverCategory
     */
    public function setDriverCategory($driverCategory) {
        $this->driverCategory = $driverCategory;
    }

    /**
     * @return DriverCategory
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
